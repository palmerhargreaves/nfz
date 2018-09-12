<?php

/**
 * calendar actions.
 *
 * @package    Servicepool2.0
 * @subpackage calendar
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class calendarActions extends ActionsWithJsonForm
{
	

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  function executeIndex(sfWebRequest $request)
  {
      $this->quarters = BudgetCalendarTable::getQuarters();
      $this->years = BudgetCalendarTable::getYearsList();
  }

  function executeShow(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

  function executeAddDate(sfWebRequest $request) 
  {
    $title = $request->getParameter('title');
    $start_date = $request->getParameter('start_date');
    $end_date = $request->getParameter('end_date');

    $calendar = new Calendar();
    $calendar->setTitle($title);
    $calendar->setStartDate($start_date);
    $calendar->setEndDate($end_date);

    $calendar->save();

    return $this->sendJson(array('status' => 1));
  }

  function executeRemoveDate(sfWebRequest $request)
  {
    CalendarTable::getInstance()->createQuery()->delete()->where('start_date = ?', $request->getParameter('start_date'))->execute(); 

    return $this->sendJson(array('status' => 1));
  }

  function executeLoadDates(sfWebRequest $request)
  {
      $items = CalendarTable::getInstance()->createQuery()->select('*')->execute();
      $result = array();

      foreach($items as $item)
        $result[] = array('title' => $item->getTitle(), 'start' => $item->getStartDate(), 'end' => date("Y-m-d", strtotime('+1 days', D::toUnix($item->getEndDate()))));

      return $this->sendJson($result); 
  }

  function executeChangeDate(sfWebRequest $request)
  {
    $old_start_date = $request->getParameter('old_start_date');
    $new_start_date = $request->getParameter('new_start_date');
    $end_date = $request->getParameter('end_date');

    $items = CalendarTable::getInstance()->createQuery()->select('*')->where('start_date = ?', $old_start_date)->execute();
    foreach($items as $item) 
    {
      $item->setStartDate($new_start_date);

      if(!empty($end_date))
        $item->setEndDate($end_date);

      $item->save();
    }

    return $this->sendJson(array('status' => 1));
  }

  function executeBudgetChangeDays(sfWebRequest $request) {
    $items = $request->getParameter('data');
    $year = $request->getParameter('year') ? $request->getParameter('year') : date('Y');

    foreach($items as $k => $item) {
      $r = BudgetCalendarTable::getInstance()->createQuery()->where('quarter = ? and year = ?', array($item['id'], $year))->execute()->getFirst();

      if(!empty($r)) {
        $r->setDay($item['day']);
        $r->save();
      }
    }

    return $this->sendJson(array('status' => 1)); 
  }

  function executeBudgetChangeYear(sfWebRequest $request) {
    return $this->sendJson(array('result' => BudgetCalendarTable::getYearQuartersAndDays($request->getPostParameter('year'))));
  }
}
