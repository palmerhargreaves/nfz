<?php

require_once dirname(__FILE__).'/../lib/user_dealersGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/user_dealersGeneratorHelper.class.php';

/**
 * user_dealers actions.
 *
 * @package    Servicepool2.0
 * @subpackage user_dealers
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class user_dealersActions extends autoUser_dealersActions
{
	function executeIndex(sfWebRequest $request)
  	{
    	$this->redirect('user');
  	}

	function executeNew(sfWebRequest $request)
	{
		parent::executeNew($request);

		$this->form->bind(array(
				'user_id' => $request->getParameter('user_id')
			), array());
	}

	function redirect($url, $statusCode = 302)
	{
		if($url == '@user_dealers_new' && $this->form)
			$url .= '?user_id='.$this->form->getValue('user_id');

		parent::redirect($url, $statusCode);
	}

	function executeCreate(sfWebRequest $request)
	{
		$d = $request->getParameter('user_dealers');
		
		parent::executeCreate($request);
	}
}

