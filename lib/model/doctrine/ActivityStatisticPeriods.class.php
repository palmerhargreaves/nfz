<?php

/**
 * ActivityStatisticPeriods
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ActivityStatisticPeriods extends BaseActivityStatisticPeriods
{
	public function getQuartersList() {
		return array_values(explode(':', $this->getQuarters()));
	}

	public function updateQuartersList($activityId, $yearVal, $values) {
		$years = D::getYearsRangeList();

    	$year = $years[$yearVal];

		$this->setYear($year);
		$this->setQuarters(implode(':', $values));

    	$this->save();
	}

	public function getCorrectYear() {
		$years = D::getYearsRangeList();

		foreach($years as $key => $year)
		{
			if($this->getYear() == $year)
				return $key;
		}

		return '';
	}


}
