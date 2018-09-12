<?php

/**
 * material module configuration.
 *
 * @package    Servicepool2.0
 * @subpackage material
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class materialGeneratorConfiguration extends BaseMaterialGeneratorConfiguration
{
	public function getPagerMaxPerPage() {
		$request = sfContext::getInstance()->getRequest();

		if($request->getParameter('all'))
			return 0;

		return parent::getPagerMaxPerPage();
	}
}
