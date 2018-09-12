<?php

/**
 * comment_stat actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class summer_service_action2Actions extends sfActions
{
	
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  function executeIndex(sfWebRequest $request)
  {
    $result = UserTable::getUsersWithSummerServiceAction2();

    $this->result = array('total' => count($result), 'items' => $result);
  
  }
  
  function executeShow(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }


  function executeAdd(sfWebRequest $request)  
  {
    $result = array();

    $dealers = DealerTable::getInstance()->getDealersList()->execute();
    foreach($dealers as $dealer) {
      $res = DealerUserServiceActionTable::getInstance()->createQuery()->select()->where('dealer_id = ?', $dealer->getId())->count();
      
      if($res == 0)
        $result[] = $dealer;
    }

    $this->dealers = $result;
  }

  function executePostData(sfWebRequest $request)
  {
    $dealerId = $request->getPostParameter('sb_dealer');
    $startDate = $request->getPostParameter('start_date');
    $endDate = $request->getPostParameter('end_date');

    $item = new DealerUserServiceAction();

    $item->setUserId($this->getUser()->getAuthUser()->getId());
    $item->setDealerId($dealerId);
    $item->setSummerServiceActionStartDate(str_replace('.', '-', $startDate));
    $item->setSummerServiceActionEndDate(str_replace('.', '-', $endDate));

    $item->save();
    $this->redirect('@summer_service_action2');
  }

}
