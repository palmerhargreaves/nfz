<?php

require_once dirname(__FILE__).'/../lib/user_groupGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/user_groupGeneratorHelper.class.php';

/**
 * user_group actions.
 *
 * @package    Servicepool2.0
 * @subpackage user_group
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class user_groupActions extends autoUser_groupActions
{
  protected $action;
  
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
    $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));
    
    parent::preExecute();
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
  
  protected function addToLog($action, $object)
  {
    $description = '';
    if($action == 'add')
      $description = 'Добавлена';
    elseif($action == 'edit')
      $description = 'Изменена';
    elseif($action == 'delete')
      $description = 'Удалена';
    
    LogEntryTable::getInstance()->addEntry(
      $this->getUser()->getAuthUser(), 
      'user_group', 
      $action, 
      'Группа пользоавтелей/'.$object->getName(),
      $description, 
      '',
      null,
      $object->getId()
    );
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
