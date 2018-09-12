<?php

require_once dirname(__FILE__).'/../lib/activity_infoGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/activity_infoGeneratorHelper.class.php';

/**
 * activity_info actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_info
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_infoActions extends autoActivity_infoActions
{
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
  
  function redirect($url, $statusCode = 302)
  {
    if($url == '@activity_info_new' && $this->form)
      $url .= '?activity_id='.$this->form->getValue('activity_id');
      
    parent::redirect($url, $statusCode);
  }
}
