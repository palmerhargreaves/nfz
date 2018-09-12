<?php

require_once dirname(__FILE__).'/../lib/newsGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/newsGeneratorHelper.class.php';

/**
 * news actions.
 *
 * @package    Servicepool2.0
 * @subpackage news
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class newsActions extends autoNewsActions
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
      
    }
  }

  public function buildQuery() {
    $query = parent::buildQuery();

    return $query->orderBy('id DESC');
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

  public function executeBatchCopy(sfWebRequest $request) 
  {
    $ids = $request->getParameter('ids');

    $items = NewsTable::getInstance()->createQuery()->whereIn('id', $ids)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    foreach($items as $item) {
      unset($item['id']);

      $item['created_at'] = date('Y-m-d H:i:s');
      $item['updated_at'] = date('Y-m-d H:i:s');
      $item['status'] = 0;

      $newItem = new News();
      $newItem->setArray($item);
      $newItem->save();
    }
    
    $this->getUser()->setFlash('notice', 'The selected items have been copied successfully.');
    $this->redirect('news');
  }

  public function executeChangeStatus(sfWebRequest $request)
  {
    $item = NewsTable::getInstance()->find($request->getParameter('id'));
    if($item) 
    {
      if($item->getStatus())
        $item->setStatus(0);
      else
        $item->setStatus(1);
      
      $item->save();
    }

    return sfView::NONE;
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
}
