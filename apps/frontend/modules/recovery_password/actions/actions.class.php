<?php

/**
 * recovery_password actions.
 *
 * @package    Servicepool2.0
 * @subpackage recovery_password
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class recovery_passwordActions extends ActionsWithJsonForm
{
  function executeRecovery(sfWebRequest $request)
  {
    $form = new RecoveryPasswordForm();
    $form->bind($request->getPostParameters());
    
    if($form->isValid())
    {
      $helper = new RecoveryPasswordHelper(UserTable::getInstance()->findOneByEmail($form->getValue('email')));
      $helper->generateKey();

      $message = new RecoveryPasswordMail($helper->getUser());
      sfContext::getInstance()->getMailer()->send($message);
    }
    
    return $this->sendFormBindResult($form);
  }
  
  function executeNewPassword(sfWebRequest $request)
  {
    $user = UserTable::getInstance()->find($request->getParameter('id'));
    if(!$user)
      return sfView::ERROR;
    
    $helper = new RecoveryPasswordHelper($user);
    if(!$helper->checkKey($request->getParameter('key')))
      return sfView::ERROR;
    
    $generator = new PasswordGenerator();
    $password = $generator->generate();
    AuthFactory::getInstance()->getAuthenticator()->setupPassword($user, $password);
    $user->save();
    
    $message = new NewPasswordMail($user, $password);
    sfContext::getInstance()->getMailer()->send($message);
    
    return sfView::SUCCESS;
  }
}
