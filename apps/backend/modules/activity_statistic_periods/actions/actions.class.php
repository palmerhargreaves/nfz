<?php

require_once dirname(__FILE__).'/../lib/activity_statistic_periodsGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/activity_statistic_periodsGeneratorHelper.class.php';

/**
 * activity_statistic_periods actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_statistic_periods
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_statistic_periodsActions extends autoActivity_statistic_periodsActions
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
		if($url == '@activity_statistic_periods_new' && $this->form)
			$url .= '?activity_id='.$this->form->getValue('activity_id');

		parent::redirect($url, $statusCode);
	}
}
