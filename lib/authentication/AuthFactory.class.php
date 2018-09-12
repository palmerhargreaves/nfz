<?php
class AuthFactory
{
  private static $instance = null;
  
  protected $authenticator = null;
  
  /**
   * Returns instance
   * 
   * @return AuthFactory
   */
  static function getInstance()
  {
    if(!self::$instance)
    {
      self::$instance = new AuthFactory();
    }
    return self::$instance;
  }
  
  /**
   * Returns authenticator
   * 
   * @return Authenticator
   */
  function getAuthenticator()
  {
    if(!$this->authenticator)
    {
      $auth_class = sfConfig::get('app_auth_class');
      
      $this->authenticator = new $auth_class();
    }
    return $this->authenticator;
  }
}