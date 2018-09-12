<?php

/**
 * dealer_services_dialogs module configuration.
 *
 * @package    Servicepool2.0
 * @subpackage dealer_services_dialogs
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dealer_services_dialogsGeneratorConfiguration extends BaseDealer_services_dialogsGeneratorConfiguration
{
	public function getPagerMaxPerPage() {
		$request = sfContext::getInstance()->getRequest();

		if($request->getParameter('all'))
			return 0;

		return parent::getPagerMaxPerPage();
	}
}
