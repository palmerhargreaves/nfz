<?php

/**
 * ActivityInfoFieldsData
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ActivityInfoFieldsData extends BaseActivityInfoFieldsData
{
	function hasData($activityId) {
		if(ActivityInfoFieldsDataTable::getInstance()
				->createQuery()
				->where('activity_id = ? and field_id = ?', array($activityId, $this->getId()))
					->count() > 0)
			return true;

		return false;
	}
}
