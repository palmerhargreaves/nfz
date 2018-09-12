<?php

/**
 * comment_stat actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class summer_service_actionActions extends sfActions
{
	const ACTIVE = 1;
	const DECLINE = 2;
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  function executeIndex(sfWebRequest $request)
  {
    $result = UserTable::getUsersWithSummerServiceAction();
	
	  $this->result = array('total' => count($result), 'items' => $result);
  
  }
  
  function executeShow(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

}
