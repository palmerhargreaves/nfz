<?php

/**
 * spring_service_action actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class service_actionsActions extends sfActions
{
	
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  const FILTER_NAMESPACE = 'services';

  function executeIndex(sfWebRequest $request)
  {
    $this->outputResult();
  }
  
  function executeShow(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

  function executeAdd(sfWebRequest $request)  
  {
    $result = array();

    $dealers = DealerTable::getInstance()->getDealersList()->execute();
    /*foreach($dealers as $dealer) {
      $res = DealersServiceDataTable::getInstance()->createQuery()->select()->where('dealer_id = ?', $dealer->getId())->count();
      
      if($res == 0)
        $result[] = $dealer;
    }*/

    $this->serviceActions = DealerServicesDialogsTable::getInstance()->createQuery()->select()->orderBy('id DESC')->execute();
    $this->dealers = $dealers;
  }

  function executeServiceDealersList(sfWebRequest $request) {
    $serviceId = $request->getPostParameter('id');

    $dealers = DealerTable::getInstance()->getDealersList()->execute();
    foreach($dealers as $dealer) {
      $res = DealersServiceDataTable::getInstance()->createQuery()->select()->where('dialog_service_id = ? and dealer_id = ?', array($serviceId, $dealer->getId()))->count();
      
      if($res == 0)
        $result[] = $dealer;
    }

    $this->dealers = $result;
  }

  function executePostData(sfWebRequest $request)
  {
    $dealerId = $request->getPostParameter('sb_dealer');
    
    $userId = $this->getUser()->getAuthUser()->getId();
    $dealer = DealerTable::getInstance()->find($dealerId);
    if($dealer) {
      $userId = $dealer->getDealerUsers()->getFirst()->getUserId();
    }

    $serviceId = $request->getPostParameter('sb_service');

    $startDate = $request->getPostParameter('start_date');
    $endDate = $request->getPostParameter('end_date');

    $item = new DealersServiceData();

    $item->setUserId($userId);
    $item->setDealerId($dealerId);
    $item->setDialogServiceId($serviceId);

    $item->setStartDate(str_replace('.', '-', $startDate));
    $item->setEndDate(str_replace('.', '-', $endDate));
    $item->setStatus('accepted');

    $item->save();
    $this->redirect('@service_index');
  }

  function executeFilterData(sfWebRequest $request)
  {
    $this->outputFilters();
    $this->outputResult();

    $this->setTemplate('index');
  }

  function executeFilterReset() {
    $this->resetFilters();
  }

  function outputData() {
    $this->activities = ActivityTable::getInstance()->createQuery()->select()->orderBy('position DESC')->execute();
    $this->serviceActions = DealerServicesDialogsTable::getInstance()->createQuery()->select()->orderBy('id DESC')->execute();
    $this->dealers =     $dealers = DealerTable::getInstance()->getDealersList()->execute();
  }

  function outputResult() 
  {
    $this->outputData();
    
    $query = DealersServiceDataTable::getInstance()->createQuery('d')->orderBy('d.id DESC');

    $dealerId = $this->getDealerFilter();
    if($dealerId != -1)
      $query->andWhere('d.dealer_id = ?', $dealerId);

    $activityId = $this->getActivityFilter();
    if($activityId != -1)
      $query->leftJoin('d.Dialog di')
            ->andWhere('di.activity_id = ?', $activityId);

    $serviceId = $this->getServiceDialogFilter();
    if($serviceId != -1)
      $query->andWhere('d.dialog_service_id = ?', $serviceId);

    if($dealerId == -1 && $activityId == -1 && $serviceId == -1)
    {
      $lastDialog = DealerServicesDialogsTable::getInstance()->createQuery()->select()->orderBy('id DESC')->limit(1)->fetchOne();
      if($lastDialog) {
        $query->andWhere('dialog_service_id = ?', $lastDialog->getId());
      }
    }

    $chDeclined = $this->getDeclinedDealersFilter();
    if($chDeclined == 1)
      $query->andWhere('status = ?', 'declined');
    else
      $query->andWhere('status = ?', 'accepted');

    $this->result = $query->execute();
  }

  function outputResetResult()
  {
    $this->outputData();
    
    $lastDialog = DealerServicesDialogsTable::getInstance()->createQuery()->select()->orderBy('id DESC')->limit(1)->fetchOne();
    if($lastDialog) {
      $query = DealersServiceDataTable::getInstance()
                                            ->createQuery('d')
                                            ->where('dialog_service_id = ?', $lastDialog->getId())
                                            ->andWhere('status = ?', 'accepted')
                                              ->orderBy('d.id DESC');
       $this->result = $query->execute();
    }
    else
      $this->result = null;
  }

  function outputFilters() 
  {
    $this->activityFilterId = $this->getActivityFilter();
    $this->serviceDialogId = $this->getServiceDialogFilter();
    $this->dealerId = $this->getDealerFilter();
    $this->isDeclined = $this->getDeclinedDealersFilter();
  }

  function resetFilters() {
    $this->getUser()->setAttribute('activity_id', -1, self::FILTER_NAMESPACE);
    $this->getUser()->setAttribute('service_id', -1, self::FILTER_NAMESPACE);
    $this->getUser()->setAttribute('dealer_id', -1, self::FILTER_NAMESPACE);
    $this->getUser()->setAttribute('is_declined', 0, self::FILTER_NAMESPACE);

    $this->outputResetResult();

    $this->setTemplate('index');
  }

  function getActivityFilter() {
      $default = $this->getUser()->getAttribute('activity_id', '-1', self::FILTER_NAMESPACE);
      $activityId = $this->getRequestParameter('sb_activities', $default);
    
      $this->getUser()->setAttribute('activity_id', $activityId, self::FILTER_NAMESPACE);
    
      return $activityId;
  }

  function getServiceDialogFilter() {
      $default = $this->getUser()->getAttribute('service_id', '-1', self::FILTER_NAMESPACE);
      $serviceId = $this->getRequestParameter('sb_service_action', $default);
    
      $this->getUser()->setAttribute('service_id', $serviceId, self::FILTER_NAMESPACE);
    
      return $serviceId;
  }

  function getDealerFilter() {
      $default = $this->getUser()->getAttribute('dealer_id', '-1', self::FILTER_NAMESPACE);
      $dealerId = $this->getRequestParameter('sb_dealers', $default);
    
      $this->getUser()->setAttribute('dealer_id', $dealerId, self::FILTER_NAMESPACE);
    
      return $dealerId;
  }

  function getDeclinedDealersFilter() {
      $default = $this->getUser()->getAttribute('is_declined', '0', self::FILTER_NAMESPACE);
      $chDeclined = $this->getRequestParameter('chDeclinedDealers', $default);
      
      $this->getUser()->setAttribute('is_declined', $chDeclined, self::FILTER_NAMESPACE);
    
      return $chDeclined; 
  }

  function executeServiceDeleteItem(sfWebRequest $request) {
    $id = $request->getPostParameter('id');

    $data = DealersServiceDataTable::getInstance()->find($id);
    if($data) {
      $data->delete();
    }

    return sfView::NONE;
  }
}
