<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.08.2016
 * Time: 15:40
 */

class ActivityCalculateEfficiencyUtils
{
    private $_activity = null;
    private $_dealer = null;
    private $_result = array();

    public function __construct(Activity $activity, User $user)
    {
        $this->_activity = $activity;

        if ($user->getDealer()) {
            $this->_dealer = $user->getDealer()->getId();

            $this->build();
        }
    }

    private function build() {
        //Check for activity & dealer is statistic is filled and send to importer
        if (ActivityDealerStaticticStatusTable::getInstance()
                ->createQuery()
                ->where('dealer_id = ? and activity_id = ?', array($this->_dealer, $this->_activity->getId()))
                ->andWhere('ignore_statistic = ?', 0)
                ->count() > 0 )
        {
            $formulas = ActivityEfficiencyFormulasTable::getInstance()->createQuery()->where('activity_id = ?', $this->_activity->getId())->orderBy('id ASC')->execute();

            foreach ($formulas as $formula) {
                $this->_result[$formula->getId()] = array('formula' => $formula, 'value' => $formula->getParamsCalculateResult($this->_dealer));
            }

        }
    }

    public function getResult() {
        return $this->_result;
    }
}
