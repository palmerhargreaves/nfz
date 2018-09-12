<?php

require_once dirname(__FILE__).'/../lib/material_web_previewGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/material_web_previewGeneratorHelper.class.php';

/**
 * material_web_preview actions.
 *
 * @package    Servicepool2.0
 * @subpackage material_web_preview
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class material_web_previewActions extends autoMaterial_web_previewActions
{
  protected $material_id = '';
  
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
    $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));
    
    parent::preExecute();
  }
  
  function executeIndex(sfWebRequest $request)
  {
    $this->redirect('@material');
  }
  
  function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);
    
    $this->form->bind(array(
      'material_id' => $request->getParameter('material_id')
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
//    $description = '';
//    if($action == 'add')
//      $description = 'Добавлен web-превью материала';
//    elseif($action == 'edit')
//      $description = 'Изменёно web-превью материала';
//    elseif($action == 'delete')
//      $description = 'Удалено web-превью материала';
//    
//    LogEntryTable::getInstance()->addEntry(
//      $this->getUser()->getAuthUser(), 
//      'material_preview', 
//      $action, 
//      'Материал/'.$object->getMaterial()->getName(), 
//      $description, 
//      $action != 'delete' ? 'clip' : '',
//      null,
//      $object->getId(),
//      'materials'
//    );
  }
  
  function redirect($url, $statusCode = 302)
  {
    if($url == '@material_web_preview_new' && $this->form)
      $url .= '?material_id='.$this->form->getValue('material_id');
      
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
