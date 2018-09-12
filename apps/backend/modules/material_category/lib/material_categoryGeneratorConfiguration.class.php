<?php

/**
 * material_category module configuration.
 *
 * @package    Servicepool2.0
 * @subpackage material_category
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class material_categoryGeneratorConfiguration extends BaseMaterial_categoryGeneratorConfiguration
{
	public function getPagerMaxPerPage() {
		$request = sfContext::getInstance()->getRequest();

		if($request->getParameter('all'))
			return 0;

		return parent::getPagerMaxPerPage();
	}
}
