<?php

/**
 * auth actions.
 *
 * @package    Servicepool2.0
 * @subpackage auth
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class authActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new AuthForm();
    
    if($request->isMethod('post'))
    {
      $this->form->bind($request->getPostParameters());
      if($this->form->isValid())
      {
        $user = UserTable::getInstance()->findOneByEmail($this->form->getValue('login'));
        $this->getUser()->login($user, $this->form->getValue('remember'));
        
        $this->redirect('home/index');
      }
    }    
  }
  
  function executeLogout(sfWebRequest $request)
  {
    $this->getUser()->logout();
    
    $this->redirect('home/index');
  }
}
