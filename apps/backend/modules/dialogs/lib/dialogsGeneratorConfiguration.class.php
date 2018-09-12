<?php

/**
 * dialogs module configuration.
 *
 * @package    Servicepool2.0
 * @subpackage dialogs
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dialogsGeneratorConfiguration extends BaseDialogsGeneratorConfiguration
{
	public function getPagerMaxPerPage() {
		$request = sfContext::getInstance()->getRequest();

		if($request->getParameter('all'))
			return 0;

		return parent::getPagerMaxPerPage();
	}
}
