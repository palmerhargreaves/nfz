<?php

/**
 * activity_extended_statistic actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_extended_statistic
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activities_statusActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */

    function executeIndex(sfWebRequest $request)
    {
        $this->dealers = DealerTable::getInstance()->getDealersList()->execute();
        $this->activities = ActivityTable::getInstance()->createQuery()->orderBy('position ASC')->execute();

        $this->blockedItems = ActivitiesStatusTable::getInstance()->createQuery()->select()->orderBy('id DESC')->execute();
    }
  

	function executeCheckActivityStatus(sfWebRequest $request)
	{
		$activity = $request->getParameter('activity');
		$dealer = $request->getParameter('dealer');

		$this->item = ActivitiesStatusTable::getInstance()->createQuery()->where('dealer_id = ? and activity_id = ?', array($dealer, $activity))->fetchOne();
	}

	function executeActivityBlockUnblock(sfWebRequest $request)
	{
		$id = $request->getParameter('id');
		if($id) {
			ActivitiesStatusTable::getInstance()->find($id)->delete();

			$this->item = null;
		}
		else {
			$activity = $request->getParameter('activity');
			$dealer = $request->getParameter('dealer');

			$item = new ActivitiesStatus();
			$item->setArray(array('activity_id' => $activity, 'dealer_id' => $dealer));
			$item->save();

			$this->item = $item;
		}

	}

	function executeActivitiesBlockedList()
	{
		$this->blockedItems = ActivitiesStatusTable::getInstance()->createQuery()->select()->orderBy('id DESC')->execute();
	}
}
