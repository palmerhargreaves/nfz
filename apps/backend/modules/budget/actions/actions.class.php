<?php

require_once dirname(__FILE__).'/../lib/budgetGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/budgetGeneratorHelper.class.php';

/**
 * budget actions.
 *
 * @package    Servicepool2.0
 * @subpackage budget
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class budgetActions extends autoBudgetActions
{
  protected $dealer_id = '';
  
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
    $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));
    
    parent::preExecute();
  }
  
  function executeIndex(sfWebRequest $request)
  {
    $this->redirect('@dealer');
  }
  
  function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);
    
    $this->form->bind(array(
      'dealer_id' => $request->getParameter('dealer_id')
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
    $name = ' за '.$object->getQuarter().' квартал '.$object->getYear().' года';
    if($action == 'add')
      $description = 'Добавлен бюджет '.$name;
    elseif($action == 'edit')
      $description = 'Изменён бюджет '.$name;
    elseif($action == 'delete')
      $description = 'Удален бюджет '.$name;
    
    LogEntryTable::getInstance()->addEntry($this->getUser()->getAuthUser(), 'budget', $action, $object->getDealer()->getName().' ('.$object->getDealer()->getNumber().')', $description, '', $object->getDealer(), $object->getId());
  }
  
  protected function recalculateRealBudget(Budget $budget)
  {
    RealTotalBudgetTable::getInstance()->recalculate($budget->getDealer(), $budget->getYear());
  }
  
  function redirect($url, $statusCode = 302)
  {
    if($url == '@budget_new' && $this->form)
      $url .= '?dealer_id='.$this->form->getValue('dealer_id');
      
    parent::redirect($url, $statusCode);
  }
  
  public function onSaveObject(sfEvent $event)
  {
    $this->addToLog($this->action, $event['object']);
    $this->recalculateRealBudget($event['object']);
  }
  
  public function onDeleteObject(sfEvent $event)
  {
    $this->addToLog('delete', $event['object']);
    $this->recalculateRealBudget($event['object']);
  }  
}
