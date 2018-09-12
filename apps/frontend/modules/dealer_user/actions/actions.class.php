<?php

/**
 * dealer_user actions.
 *
 * @package    Servicepool2.0
 * @subpackage dealer_user
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dealer_userActions extends sfActions
{
  /**
   * Dealer
   *
   * @var Dealer
   */
  protected $dealer = null;
  
  function preExecute()
  {
    


  }
  
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
      $dealer_user = $this->getUser()->getAuthUser()->getDealerUsers()->getFirst();

      if(!$dealer_user || !$dealer_user->getManager())
        throw new UserIsNotManagerException($this->getUser()->getAuthUser());
    
      $this->dealer = $dealer_user->getDealer();
    
      if(!$this->dealer)
        throw new UserIsNotManagerException($this->getUser()->getAuthUser());

      $this->users = UserTable::getInstance()
                   ->createQuery('u')
                   ->select('u.*')
                   ->innerJoin('u.DealerUsers du with dealer_id=?', $this->dealer->getId())
                   ->execute();
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    $user = UserTable::getInstance()->find($request->getParameter('id'));
    if(!$user)
      $this->redirect('dealer_user/index');

    $dealer_user = $this->getUser()->getAuthUser()->getDealerUsers()->getFirst();

    if(!$dealer_user || !$dealer_user->getManager())
      $this->redirect('dealer_user/index');
  
    $this->dealer = $dealer_user->getDealer();
  
    if(!$this->dealer)
      $this->redirect('dealer_user/index');

    $dealer_user = $user->getDealerUsers()->getFirst();
    if(!$dealer_user || $dealer_user->getDealerId() != $this->dealer->getId())
      $this->redirect('dealer_user/index');
    
    $user->delete();
    
    LogEntryTable::getInstance()->addEntry(
      $this->getUser()->getAuthUser(), 
      'dealer_user', 
      'delete', 
      'Пользователи',
      'Удалён пользователь "'.$user->getEmail().'"', 
      '',
      $this->dealer,
      $user->getId()
    );
    
    $this->redirect('dealer_user/index');
  }
  
  public function executeActivate(sfWebRequest $request)
  {
    $user = UserTable::getInstance()->find($request->getParameter('id', 0));
    if(!$user)
    {
      $this->error = 'Пользователь не найден.';
      return sfView::ERROR;
    }
    if(!$user->checkActivationKey($request->getParameter('key')))
    {
      $this->error = 'Неверный ключ активации.';
      return sfView::ERROR;
    }
    
    $user->resetActivationKey();
    $user->setActive(true);
    $user->save();
    
    $message = new UserActivationMail($user);
    $message->setPriority(1);
    sfContext::getInstance()->getMailer()->send($message);
    
    $this->user = $user;
  }
  
  public function executeDeactivate(sfWebRequest $request)
  {
    $user = UserTable::getInstance()->find($request->getParameter('id', 0));
    if(!$user)
    {
      $this->error = 'Пользователь не найден.';
      return sfView::ERROR;
    }
    if(!$user->checkActivationKey($request->getParameter('key')))
    {
      $this->error = 'Неверный ключ деактивации.';
      return sfView::ERROR;
    }
    
    $message = new UserDeactivationMail($user);
    $message->setPriority(1);
    sfContext::getInstance()->getMailer()->send($message);
    
    $user->delete();
    
    $this->user = $user;    
  }
}
