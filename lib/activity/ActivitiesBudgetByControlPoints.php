<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 24.09.2017
 * Time: 12:52
 */
class ActivitiesBudgetByControlPoints
{
    private $_year = null;
    private $_dealer = null;
    private $_user = null;

    private $_real_budget = array();
    private $_fact_budget = array();

    private $_quarters_statistics = array();

    const ACTIVITY_NOT_START = 'red';
    const ACTIVITY_IN_WORK = 'orange';
    const ACTIVITY_COMPLETED_WITHOUT_STATISTIC = 'yellow';
    const ACTIVITY_TOTAL_COMPLETED = 'green';

    /**
     * ActivitiesBudgetByControlPoints constructor.
     * @param $year
     * @param $dealer
     * @param $user
     * @param $real_budget
     * @param $fact_budget
     */
    public function __construct ( $year, $dealer, $user, $real_budget, $fact_budget )
    {
        $this->_year = $year;
        $this->_dealer = $dealer;
        $this->_user = $user;

        $this->_real_budget = $real_budget;
        $this->_fact_budget = $fact_budget;

        $this->init();
        $this->calc();
    }

    /**
     * Инициализация данных по умолчанию
     */
    private function init ()
    {
        for ($q = 1; $q <= 4; $q++) {
            $this->_quarters_statistics[ $q ] = array(
                'quarter_completed' => false,
                'current_quarter' => false,
                'quarter_plan_completed' => false,
                'mandatory_activities' => array(
                    'completed' => false,
                    'completed_count' => 0,
                    'list' => array()
                ),
                'emails_completed' => false,
                'year' => $this->_year,
            );
        }
    }

    /**
     * Вычисление статистики по параметрам
     */
    protected function calc ()
    {
        //Вычисляем выполнение по кварталам
        if (!empty($this->_real_budget) && !empty($this->_fact_budget)) {
            for ($q = 1; $q <= 4; $q++) {
                $this->_quarters_statistics[ $q ][ 'quarter_plan_completed' ] = $this->_real_budget[ $q ] > 0 && $this->_real_budget[ $q ] >= $this->_fact_budget[ $q ]->getPlan();
            }

            //Делаем доп. проход по выполненным бюджетам
            //Отмечаем выполнение бюджета, если бюджет за пред. кварталы выполнен
            /*foreach ($this->_quarters_statistics as $q => $completed) {
                for ($q_ind = 1; $q_ind <= $q; $q_ind++) {
                    $this->_quarters_statistics[$q]['quarter_plan_completed'] = $this->_quarters_statistics[$q_ind]['quarter_plan_completed'];
                }
            }*/
        }

        //Вычисляем выполнение обязательных активностей
        $mandatory_activities = ActivityTable::getInstance()->createQuery()->where('mandatory_activity = ?', true)->execute();
        for ($q = 1; $q <= 4; $q++) {
            //Делаем проверку на наличие слотов в квартале / году
            $slots = QuartersSlotsTable::getInstance()->createQuery()->where('quarter = ? and year = ?', array( $q, $this->_year ))->execute();

            if (count($slots)) {
                $slots_result = array();

                foreach ($slots as $slot) {
                    $activities = $slot->getSlotActivities();

                    $slots_result[$q][$slot->getId()] = array("activities_count" => count($activities), "activities_completed" => 0);

                    foreach ($activities as $slot_activity) {
                        $activity = $slot_activity->getActivity();
                        $status = $activity->getStatusByQuarter($this->_user->getAuthUser(), $q, true, $this->_year);

                        //Получение информации по выполнению активностей
                        $work_status_result = $activity->getStatusBudgetPointsByQuarter($this->_user->getAuthUser(), $q, true, $this->_year);

                        $work_status = $work_status_result['status'];
                        $work_status_msg = $work_status_result['msg'];


                        if ($status != ActivityModuleDescriptor::STATUS_NONE) {
                            //var_dump($q.'--'.$status.'--'.$activity->getId());
                            $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed' ] = $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed' ] && $status == ActivityModuleDescriptor::STATUS_ACCEPTED;

                            //Учитываем количество выполненных обязательных активностей
                            //Если 3 активности выполнено учитываем это в квартале
                            if ($status == ActivityModuleDescriptor::STATUS_ACCEPTED) {
                                $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed_count' ]++;
                                $slots_result[$q][$slot->getId()]["activities_completed"]++;
                            }

                            if ($this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed_count' ] >= 3) {
                                $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed' ] = true;
                            }
                        }

                        $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'list' ][] = array
                        (
                            'id' => $activity->getId(),
                            'name' => $activity->getName(),
                            'completed' => $status == ActivityModuleDescriptor::STATUS_ACCEPTED,
                            'work_status' => $work_status,
                            'work_status_msg' => $work_status_msg,
                            'can_redirect' => $activity->getModules()->count() > 0
                        );
                    }
                }

                //Проходим по слотав в кварталах и провряем на вополнение условий
                //В каждом слоте должна быть выполнена одна активность
                /*foreach ($slots_result as $slot_q => $slot_items) {
                    $q_completed = false;

                    foreach ($slot_items as $slot_item_data) {
                        if ($slot_item_data["activities_completed"] > 0) {
                            $q_completed = true;
                        }
                    }
                    $this->_quarters_statistics[ $slot_q ][ 'mandatory_activities' ][ 'completed' ] = $q_completed;
                }*/

            } else {
                foreach ($mandatory_activities as $activity) {
                    /**
                     * Activity $activity
                     */

                    //Проверяем на обязательную активность для квартала
                    $quarters = MandatoryActivityQuartersTable::getInstance()->createQuery()->where('activity_id = ? and year = ?', array( $activity->getId(), $this->_year ))->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
                    if ($quarters) {
                        $quarters_list = explode(":", $quarters[ 'quarters' ]);

                        if (in_array($q, $quarters_list)) {
                            $status = $activity->getStatusByQuarter($this->_user->getAuthUser(), $q, true, $this->_year);
                            if ($status != ActivityModuleDescriptor::STATUS_NONE) {
                                //var_dump($q.'--'.$status.'--'.$activity->getId());
                                $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed' ] = $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed' ] && $status == ActivityModuleDescriptor::STATUS_ACCEPTED;

                                //Учитываем количество выполненных обязательных активностей
                                //Если 3 активности выполнено учитываем это в квартале
                                if ($status == ActivityModuleDescriptor::STATUS_ACCEPTED) {
                                    $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed_count' ]++;
                                }

                                if ($this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed_count' ] >= 3) {
                                    $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed' ] = true;
                                }
                            }

                            $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'list' ][] = array
                            (
                                'id' => $activity->getId(),
                                'name' => $activity->getName(),
                                'completed' => $status == ActivityModuleDescriptor::STATUS_ACCEPTED
                            );
                        }
                    }
                }
            }

            /**
            //Вычисляем загрузку email адресов
            $dealer_mailing_plan = $this->getDealerPlan($this->_dealer->getNumber(), implode(',', D::getQuarterMonths($q)), $this->_year);
            $months_plan = array();

            //Проверяем на загрузку почтовых адресов во всех месяцов квартала
            //Проверяем на загрузку планов по почтовым адресам
            $months_completed = array();
            foreach (D::getQuarterMonths($q) as $month) {
                //$months_completed[$q] = $this->getMailings($this->_dealer->getNumber(), $month, $this->_year) > 0;
                $months_plan[ $q ] = $this->getDealerPlan($this->_dealer->getNumber(), $month, $this->_year) > 0;
            }

            $dealer_mailing_real = $this->getMailings($this->_dealer->getNumber(), implode(',', D::getQuarterMonths($q)), $this->_year);

            if ($dealer_mailing_plan > 0 && isset($months_plan[$q]) && $months_plan[$q]) {
                $this->_quarters_statistics[ $q ][ 'emails_completed' ] = ( $dealer_mailing_real * 100 / $dealer_mailing_plan ) > 49;
            }
             */
        }

        //Обобщаем информацию по кварталам, информируем дилера о статусе выполнение условий по кварталу
        $current_quarter = D::getQuarter(D::calcQuarterData(date('Y-m-d H:i:s')));
        for ($q = 1; $q <= 4; $q++) {
            //Учитываем выполнение бюджета
            $this->_quarters_statistics[ $q ][ 'quarter_completed' ] = $this->_quarters_statistics[ $q ][ 'quarter_plan_completed' ];

            //Учитываем выполнение обязательных активностей, если они есть в квартале
            if (count($this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'list' ]) && $this->_quarters_statistics[ $q ][ 'quarter_completed' ]) {
                //var_dump($q.'---'.$this->_quarters_statistics[$q]['mandatory_activities']['completed']);
                $this->_quarters_statistics[ $q ][ 'quarter_completed' ] = $this->_quarters_statistics[ $q ][ 'mandatory_activities' ][ 'completed' ];
            }

            //Учитываем выполнение по загрузке почтовых адресов
            /**$this->_quarters_statistics[ $q ][ 'quarter_completed' ] = $this->_quarters_statistics[ $q ][ 'emails_completed' ] && $this->_quarters_statistics[ $q ][ 'quarter_completed' ];

            if (!$this->_quarters_statistics[ $q ][ 'quarter_completed' ] && $q != $current_quarter) {
                $this->_quarters_statistics[ $q ][ 'current_quarter' ] = false;
            }
             */

        }
    }

    public function getData ()
    {
        return $this->_quarters_statistics;
    }

    /**
     * План дилеров
     * @param $dealer_number
     * @param $months
     * @param $year
     * @return mixed
     */
    private function getDealerPlan ( $dealer_number, $months, $year )
    {
        $total_plan = 0;

        $plan = DealerPlansTable::getInstance()->createQuery()
            ->select('MONTH(added_date) as month, SUM(plan1 + plan2) as plan')
            ->where('dealer_id = ' . $dealer_number . ' AND MONTH(added_date) IN (' . $months . ') AND YEAR(added_date) IN (' . $year . ')')
            ->groupBy('MONTH(added_date)')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($plan as $plan_item) {
            $total_plan += $plan_item[ 'plan' ];
        }

        return $total_plan;
    }

    /**
     * Список загруженных емейлов
     * @param $dealer_number
     * @param $months
     * @param $year
     * @return mixed
     */
    private function getMailings ( $dealer_number, $months, $year )
    {
        $total_mailings = 0;

        $mailings = MailingListTable::getInstance()->createQuery()
            ->select('MONTH(added_date) as month, count(*) as count')
            ->where('dealer_id = ' . $dealer_number . ' AND MONTH(added_date) IN (' . $months . ') AND YEAR(added_date) IN (' . $year . ')')
            ->groupBy('MONTH(added_date)')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($mailings as $mailing_item) {
            $total_mailings += $mailing_item[ 'count' ];
        }

        return $total_mailings;
    }
}
