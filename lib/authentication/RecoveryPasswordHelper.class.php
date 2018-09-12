<?php

/**
 * Description of PasswordRecoveryHelper
 *
 * @author Сергей
 */
class RecoveryPasswordHelper
{
  /**
   * User
   *
   * @var User
   */
  protected $user;
  
  function __construct(User $user)
  {
    $this->user = $user;
  }
  
  function generateKey()
  {
    $this->user->setRecoveryKey(md5(
      'slfnegnfefwmdsk'.'-'.mt_rand(10000, 99999).'-'.$this->user->getEmail().'-'.$this->user->get('updated_at').'-'.time().'-'.mt_rand(10000, 99999).'-'.',mcn,xmncpkp[qw'
    ));
    $this->user->save();
  }
  
  function checkKey($key, $auto_clean = true)
  {
    $result = $this->user->getRecoveryKey() && $this->user->getRecoveryKey() == $key;
    
    if($auto_clean)
    {
      $this->user->setRecoveryKey('');
      $this->user->save();
    }
    
    return $result;
  }
  
  /**
   * Returns user
   * 
   * @return User
   */
  function getUser()
  {
    return $this->user;
  }
}
