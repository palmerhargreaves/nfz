<?php

require_once dirname(__FILE__).'/../lib/agreement_model_blankGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/agreement_model_blankGeneratorHelper.class.php';

/**
 * agreement_model_blank actions.
 *
 * @package    Servicepool2.0
 * @subpackage agreement_model_blank
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agreement_model_blankActions extends autoAgreement_model_blankActions
{
  protected $action;
  
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
    $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));
    
    parent::preExecute();
  }
  
  function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);
    
    $this->form->bind(array(
      'activity_id' => $this->getActivityId()
    ), array());
  }
  
  public function executeCreate(sfWebRequest $request)
  {
    $this->action = 'add';
    
    parent::executeCreate($request);
  }
  
  public function executeUpdate(sfWebRequest $request)
  {
    $this->action = 'edit';
    
    parent::executeUpdate($request);
  }
  
  protected function buildQuery()
  {
    return parent::buildQuery()->andWhere(
      'activity_id=?', $this->getActivityId()
    );
  }  
  
  protected function addToLog($action, $object)
  {
    $description = '';
    if($action == 'add')
      $description = 'Добавлена болванка "'.$object->getName().'"';
    elseif($action == 'edit')
      $description = 'Изменена болванка "'.$object->getName().'"';
    elseif($action == 'delete')
      $description = 'Удалена болванка "'.$object->getName().'"';

    $entry = LogEntryTable::getInstance()->addEntry(
      $this->getUser()->getAuthUser(), 
      'agreement_model_blank', 
      $action, 
      $object->getActivity()->getName(), 
      $description, 
      '', 
      null, 
      $object->getId(), 
      'agreement'
    );
    $entry->save();
  }
  
  /**
   * Returns activity
   * 
   * @return Activity
   */
  protected function getActivity()
  {
    return ActivityTable::getInstance()->find($this->getActivityId());
  }
  
  protected function getActivityId()
  {
    return $this->getUser()->getAttribute('activity_id', 0, 'agreement_module');
  }
  
  public function onSaveObject(sfEvent $event)
  {
    $this->addToLog($this->action, $event['object']);
  }
  
  public function onDeleteObject(sfEvent $event)
  {
    $this->addToLog('delete', $event['object']);
  }
}
