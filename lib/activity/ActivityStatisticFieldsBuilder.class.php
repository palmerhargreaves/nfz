<?php

/**
 * Description of ActivityStatisticFieldsBuilder
 *
 *
 */
class ActivityStatisticFieldsBuilder
{
    protected $year;
    protected $quarter = null;
    protected $stat = array();

    protected $activity = null;
    protected $user = null;

    protected $dealers = null;

    function __construct($dateFilter, $activity, $user)
    {
        $this->year = $dateFilter['year'];

        if (isset($dateFilter['quarter']) && ($dateFilter['quarter'] != -1 && $dateFilter['quarter'] != 0)) {
            $this->quarter = $dateFilter['quarter'];
        }

        $this->activity = $activity;
        $this->user = $user;

        $this->loadDealers();

        $this->build();
        $this->buildByActivity();
        $this->buildFields();
    }

    function loadDealers()
    {
        if ($this->dealers !== null)
            return;

        $this->dealers = array();

        foreach (DealerTable::getVwDealersQuery()->execute() as $dealer) {
            $this->dealers[$dealer->getId()] = $dealer;
        }
    }

    function build()
    {
        $this->stat = array();

        $query = ActivityTable::getInstance()
            ->createQuery('a')
            ->select('a.id, a.start_date, a.end_date, a.custom_date, a.name, a.brief, a.importance')
            ->innerJoin('a.ActivityField af')
            ->orderBy('a.importance DESC, sort DESC, a.id DESC');


        $result = $query->execute();
        foreach ($result as $item) {
            if ($item->getActivityField()->count() > 0) {
                $this->stat['activities'][] = $item;
            }
        }

        return $this->stat;
    }

    function buildByActivity()
    {
        $this->stat['dealers'] = array();
        $this->stat['quarters'] = array();

        if (!$this->activity) {
            return $this->stat;
        }

        if (empty($this->activity) && isset($this->stat['activities']) && count($this->stat['activities']) > 0)
            $this->activity = $this->stat['activities'][0];

        $query = ActivityFieldsValuesTable::getInstance()->createQuery('v')
            ->select('v.dealer_id, v.val, v.field_id, v.q, v.updated_at')
            ->innerJoin('v.ActivityFields f')
            ->where('f.activity_id = ?', $this->activity->getId())
            ->andWhere('v.updated_at != ?', '')
            ->orderBy('f.id ASC');

        $result = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $totalQ = 0;
        foreach ($result as $item) {
            if (!array_key_exists($item['q'], $this->stat['quarters'])) {
                $this->stat['quarters'][$item['q']] = $item['q'];
            }

            if (isset($this->quarter) && $this->quarter != $item['q']) {
                continue;
            }

            if (array_key_exists($item['dealer_id'], $this->dealers)) {
                if (empty($this->stat['dealers'][$item['q']][$item['dealer_id']]['dealer'])) {
                    $this->stat['dealers'][$item['q']][$item['dealer_id']]['dealer'] = $this->dealers[$item['dealer_id']];

                    if ($item['q'] != 0) {
                        $totalQ++;
                    }
                }

                if (!isset($this->stat['dealers'][$item['q']][$item['dealer_id']]['values'])) {
                    $this->stat['dealers'][$item['q']][$item['dealer_id']]['values'] = array();
                }

                if(!in_array($item['field_id'], $this->stat['dealers'][$item['q']][$item['dealer_id']]['values'])) {
                    $this->stat['dealers'][$item['q']][$item['dealer_id']]['values']['item'][] = $item;
                    $this->stat['dealers'][$item['q']][$item['dealer_id']]['values'][$item['field_id']] = $item['field_id'];
                }

                $this->stat['dealers'][$item['q']][$item['dealer_id']]['update_date'] = $item['updated_at'];
            }
        }

        $this->stat['totalQ'] = $totalQ;
    }

    function buildFields()
    {
        if ($this->activity) {
            $this->stat['fields'] = ActivityFieldsTable::getInstance()->createQuery()->where('activity_id = ?', $this->activity->getId())->orderBy('id ASC')->execute();
        }
    }

    function getStat()
    {
        return $this->stat;
    }

    function getYear()
    {
        return $this->year;
    }

    function getUser()
    {
        return $this->user;
    }
}
