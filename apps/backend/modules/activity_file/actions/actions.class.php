<?php

require_once dirname(__FILE__).'/../lib/activity_fileGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/activity_fileGeneratorHelper.class.php';

/**
 * activity_file actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_file
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_fileActions extends autoActivity_fileActions
{
  protected $activity_id = '';
  
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
    $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));
    
    parent::preExecute();
  }
  
  function executeIndex(sfWebRequest $request)
  {
    $this->redirect('@activity');
  }
  
  function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);
    
    $this->form->bind(array(
      'activity_id' => $request->getParameter('activity_id')
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
  
  protected function addToLog($action, $object)
  {
    $description = '';
    if($action == 'add')
      $description = 'Добавлен файл "'.$object->getName().'"';
    elseif($action == 'edit')
      $description = 'Изменён файл '.$object->getName().'"';
    elseif($action == 'delete')
      $description = 'Удалён файл "'.$object->getName().'"';
    
    LogEntryTable::getInstance()->addEntry($this->getUser()->getAuthUser(), 'activity_file', $action, $object->getActivity()->getName(), $description, 'clip', null, $object->getId());
  }
  
  function redirect($url, $statusCode = 302)
  {
    if($url == '@activity_file_new' && $this->form)
      $url .= '?activity_id='.$this->form->getValue('activity_id');
      
    parent::redirect($url, $statusCode);
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
