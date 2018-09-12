<?php
/**
 * Basic authenticator
 *
 * @author Сергей
 */
class BasicAuthenticator implements Authenticator
{
  function auth($login, $password)
  {
    $user = UserTable::getInstance()->findOneByEmail($login);
    if(!$user || !$user->getActive())
      return false;
    
    return $user->getPassword() == $this->doHash($password);
  }
  
  function setupPassword(User $user, $password)
  {
    $user->setPassword($this->doHash($password));
  }
  
  protected function doHash($password)
  {
    return md5($password);
  }
}
