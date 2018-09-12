<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 23.08.2016
 * Time: 14:30
 */

class ActivitiesEfficiencyDealersStatistic
{
    private $_filter = array();
    private $_activity = null;
    private $_activities = array();

    private $_user = null;
    private $_dealers = null;

    private $_formulas = array();
    private $_result =array();

    public function __construct($filter, $activity, User $user)
    {
        $this->_filter = $filter;

        if (is_null($activity)) {
            $this->getActivities();
            $this->_activity = $this->_activities[0];
        }
        else {
            $this->_activity = $activity;
        }

        $this->_user = $user;

        $this->loadDealers();
    }

    public function build() {
        $formulas = ActivityEfficiencyFormulasTable::getInstance()
            ->createQuery('f')
            ->innerJoin('f.ActivityEfficiencyWorkFormulas pf')
            ->where('activity_id = ?', $this->_activity->getId())
            ->orderBy('pf.position ASC')
            ->execute();

        $this->_result = array();
        if (count($formulas) > 0) {
            foreach ($formulas as $formula) {
                foreach ($this->_dealers as $dealer) {
                    if (AgreementModelTable::getInstance()->createQuery()->where('dealer_id = ? and activity_id = ?', array($dealer->getId(), $this->_activity->getId()))->count() > 0) {
                        if (ActivityFieldsValuesTable::getInstance()->createQuery('fv')
                                ->leftJoin('fv.ActivityFields af')
                                ->where('af.activity_id = ? and fv.dealer_id = ?', array($this->_activity->getId(), $dealer->getId()))
                                ->count() > 0
                        ) {
                            $this->_result[$dealer->getId()][$formula->getId()] = $formula->getParamsCalculateResult($dealer->getId());
                        }
                    }
                }

                $this->_formulas[] = $formula;
            }
        }

        return array();
    }

    public function getResults() {
        return array('formulas' => $this->_formulas, 'results' => $this->_result, 'activities' => $this->getActivities());
    }

    private function getActivities() {

        $this->_activities = array();
        $query = ActivityTable::getInstance()
            ->createQuery('a')
            ->select('a.id, a.start_date, a.end_date, a.custom_date, a.name, a.brief, a.importance')
            ->innerJoin('a.ActivityField af')
            ->innerJoin('a.Formulas f')
            ->orderBy('a.importance DESC, sort DESC, a.id DESC');
        $result = $query->execute();

        foreach ($result as $item) {
            if ($item->getActivityField()->count() > 0) {
                $this->_activities[] = $item;
            }
        }

        return $this->_activities;
    }

    function loadDealers()
    {
        if ($this->_dealers !== null)
            return;

        $this->_dealers = array();

        $user_dealers_list = array();
        if ($this->_user->isRegionalManager()) {
            $userDealers = $this->_user->hasDealersListFromNaturalPerson();

            foreach ($userDealers as $k => $i) {
                $user_dealers_list[] = $k;
            }
        }

        $query = DealerTable::getVwDealersQuery();

        if (!empty($user_dealers_list)) {
            $query->andWhereIn('d.id', $user_dealers_list);
        }

        foreach ($query->execute() as $dealer) {
            $this->_dealers[$dealer->getId()] = $dealer;
        }
    }
}