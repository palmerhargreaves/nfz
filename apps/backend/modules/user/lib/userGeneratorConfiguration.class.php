<?php

/**
 * user module configuration.
 *
 * @package    Servicepool2.0
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userGeneratorConfiguration extends BaseUserGeneratorConfiguration
{
  function getForm($object = null, $options = array())
  {
    $form = parent::getForm($object, $options);
    
    if(!$this->isAdmin())
      unset($form['group_id']);
    
    return $form;
  }  
  
  function getFilterForm($filters)
  {
    $form = parent::getFilterForm($filters);
    
    if(!$this->isAdmin())
      unset($form['group_id']);
    
    return $form;
  }
  
  protected function isAdmin()
  {
    return sfContext::getInstance()->getUser()->hasCredential('admin');
  }

  public function getPagerMaxPerPage() {
    $request = sfContext::getInstance()->getRequest();

    if($request->getParameter('all'))
      return 0;

    return parent::getPagerMaxPerPage();
  }
}
