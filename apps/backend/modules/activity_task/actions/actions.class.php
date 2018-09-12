<?php

require_once dirname(__FILE__).'/../lib/activity_taskGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/activity_taskGeneratorHelper.class.php';

/**
 * activity_task actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_task
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_taskActions extends autoActivity_taskActions
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
  
  public function executeDealerTasks(sfWebRequest $request)
  {
    $this->tasks = ActivityTaskTable::getInstance()
                   ->createQuery('at')
                   ->leftJoin('at.Results atr WITH dealer_id=?', $request->getParameter('dealer_id'))
                   ->where('at.activity_id=?', $request->getParameter('activity_id'))
                    ->orderBy('position ASC')
                   ->execute();
    
    $this->dealer_id = $request->getParameter('dealer_id');
  }
  
  public function executeAcceptDealerTasks(sfWebRequest $request)
  {
    $result = $this->getTaskResult($request);
    $result->setDone(true);
    $result->save();
    
    $this->sheduleUpdatingActivityStatusByTaskResult($result);
    
    $task = ActivityTaskTable::getInstance()->find($result->getTaskId());
    $dealer = DealerTable::getInstance()->find($result->getDealerId());
    
    LogEntryTable::getInstance()->addEntry(
      $this->getUser()->getAuthUser(), 
      'activity_task', 
      'assepted_activity_task', 
      $task->getActivity()->getName(), 
      'Завершена задача "'.$task->getName().'"', 
      'ok', 
      $dealer, 
      $task->getId(),
      '',
      true
    );
    
    return sfView::NONE;
  }
  
  public function executeCancelDealerTasks(sfWebRequest $request)
  {
    $result = $this->getTaskResult($request);
    $result->setDone(false);
    $result->save();
    
    $this->sheduleUpdatingActivityStatusByTaskResult($result);
    
    $task = ActivityTaskTable::getInstance()->find($result->getTaskId());
    $dealer = DealerTable::getInstance()->find($result->getDealerId());
    
    LogEntryTable::getInstance()->addEntry(
      $this->getUser()->getAuthUser(), 
      'activity_task', 
      'cancel_activity_task', 
      $task->getActivity()->getName(), 
      'Отменено завершение задачи "'.$task->getName().'"', 
      '', 
      $dealer, 
      $task->getId(),
      '',
      true
    );
    
    return sfView::NONE;
  }
  
  protected function addToLog($action, $object)
  {
    $description = '';
    if($action == 'add')
      $description = 'Добавлена задача "'.$object->getName().'"';
    elseif($action == 'edit')
      $description = 'Изменена задача "'.$object->getName().'"';
    elseif($action == 'delete')
      $description = 'Удалена задача "'.$object->getName().'"';
    
    LogEntryTable::getInstance()->addEntry($this->getUser()->getAuthUser(), 'activity_task', $action, $object->getActivity()->getName(), $description, '', null, $object->getId());
  }
  
  function redirect($url, $statusCode = 302)
  {
    if($url == '@activity_task_new' && $this->form)
      $url .= '?activity_id='.$this->form->getValue('activity_id');
      
    parent::redirect($url, $statusCode);
  }
  
  /**
   * Returns task result
   * 
   * @return ActivityTaskResult 
   */
  protected function getTaskResult(sfWebRequest $request)
  {
    $result = ActivityTaskResultTable::getInstance()
              ->createQuery()
              ->where('task_id=? and dealer_id=?', array($request->getParameter('task_id'), $request->getParameter('dealer_id')))
              ->fetchOne();
    
    if(!$result)
    {
      $result = new ActivityTaskResult();
      $result->setArray(array(
        'task_id' => $request->getParameter('task_id'),
        'dealer_id' => $request->getParameter('dealer_id'),
      ));
    }
    
    return $result;
  }
  
  protected function sheduleUpdatingActivityStatusByTask(ActivityTask $task)
  {
    foreach(DealerTable::getVwDealersQuery()->execute() as $dealer)
      UpdateActivityStatusTable::getInstance()->shedule($task->getActivity(), $dealer);
  }
  
  protected function sheduleUpdatingActivityStatusByTaskResult(ActivityTaskResult $result)
  {
    UpdateActivityStatusTable::getInstance()->shedule($result->getActivityTask()->getActivity(), $result->getDealer());
  }
  
  public function onSaveObject(sfEvent $event)
  {
    $this->addToLog($this->action, $event['object']);
    
    $this->sheduleUpdatingActivityStatusByTask($event['object']);
  }
  
  public function onDeleteObject(sfEvent $event)
  {
    $this->addToLog('delete', $event['object']);
    
    $this->sheduleUpdatingActivityStatusByTask($event['object']);
  }  

  public function executeActivityTasks(sfWebRequest $request) {
      $this->tasks = ActivityTaskTable::getInstance()
                   ->createQuery('at')
                   ->where('at.activity_id=?', $request->getParameter('activity_id'))
                    ->orderBy('position ASC')
                   ->execute();

      $this->activityId = $request->getParameter('activity_id');
  }

  public function executeActivityTasksOrders(sfWebRequest $request)
  {
    $elements = $request->getParameter('elements');
    $activityId = $request->getParameter('activityId');

    foreach($elements as $key => $element) {
      $task = ActivityTaskTable::getInstance()->find($element['id']);

      if($task) {
        $task->setPosition($element['position']);
        $task->save();
      }
    }
    
    return sfView::NONE;
  }
}
