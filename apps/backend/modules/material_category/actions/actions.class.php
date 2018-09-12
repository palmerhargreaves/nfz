<?php

require_once dirname(__FILE__).'/../lib/material_categoryGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/material_categoryGeneratorHelper.class.php';

/**
 * material_category actions.
 *
 * @package    Servicepool2.0
 * @subpackage material_category
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class material_categoryActions extends autoMaterial_categoryActions
{
  protected $action;
  
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
    $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));
    
    parent::preExecute();
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    try 
    {
      parent::executeDelete($request);
    }
    catch(CantDeleteWithRelationException $e)
    {
      $this->getUser()->setFlash('error', 'Нельзя удалить категорию, в которой есть материалы..');
      $this->redirect('@material_category');
    }
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
    $query = parent::buildQuery();

    $query->orderBy('category_order ASC');

    return $query;
  } 
  
  protected function addToLog($action, $object)
  {
//    $description = '';
//    if($action == 'add')
//      $description = 'Добавлена';
//    elseif($action == 'edit')
//      $description = 'Изменена';
//    elseif($action == 'delete')
//      $description = 'Удалена';
//    
//    LogEntryTable::getInstance()->addEntry(
//      $this->getUser()->getAuthUser(), 
//      'material_category', 
//      $action, 
//      'Категория материалов/'.$object->getName(), 
//      $description, 
//      '', 
//      null, 
//      $object->getId(),
//      'materials'
//    );
  }
  
  public function onSaveObject(sfEvent $event)
  {
    $this->addToLog($this->action, $event['object']);
  }
  
  public function onDeleteObject(sfEvent $event)
  {
    $this->addToLog('delete', $event['object']);
  }

  public function executeReorderMaterialCategory(sfWebRequest $request)
  {
    $materials = $request->getParameter('elements');
    
    foreach($materials as $mat) {
      $material = MaterialCategoryTable::getInstance()->find($mat['id']);
      $material->setCategoryOrder($mat['position']);
      $material->save();
    }

    $this->redirect('material_category');
  }


  
}
