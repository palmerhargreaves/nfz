<?php

/**
 * Description of SessionStorrageForUpload
 *
 * @author Сергей
 */
class SessionStorageForUpload extends sfSessionStorage
{
  public function initialize($options = null)
  {
    ini_set('session.use_cookies', '0');
    
    $request = sfContext::getInstance()->getRequest();
    parent::initialize(array(
      'session_id' => $request->getParameter('symfony')
    ));
  }
}
