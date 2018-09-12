<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 20.03.2018
 * Time: 13:02
 */

class ActivityBudgetPointsStatuses extends ActivityStatusBase {

    public function getStatus()
    {
        //Получаем данные по заявкам и статистики
        parent::getStatus();

        //Проверка на выполнение для активностей с статистикой
        if ($this->activity_models_created_count > 0) {
            $activity_completed = Utils::checkModelsCompleted($this->activity, $this->dealer, $this->year, $this->quarter);

            if ($this->fields_values > 0) {

                $activity_statistic_completed = $this->activity->isActivityStatisticComplete($this->dealer, null, true, $this->year, $this->quarter, $this->consider_activity_quarter ? array('check_by_quarter' => true) : null);

                //Полное выполнение активности и статистики
                if ($activity_completed && $activity_statistic_completed) {
                    return array( 'status' => ActivitiesBudgetByControlPoints::ACTIVITY_TOTAL_COMPLETED, 'msg' => 'Активность выполнена, статистика заполнена' );
                }

                //Выполнены только заявки, статистика не заполнена
                if ($activity_completed && !$activity_statistic_completed) {
                    return array( 'status' => ActivitiesBudgetByControlPoints::ACTIVITY_COMPLETED_WITHOUT_STATISTIC, 'msg' => 'Активность выполнена, но статистика не заполнена' );
                }
            } else if ($activity_completed) {
                return array( 'status' => ActivitiesBudgetByControlPoints::ACTIVITY_TOTAL_COMPLETED, 'msg' => 'Активность выполнена' );
            }

            //В активности есть созданные заявки
            return array( 'status' => ActivitiesBudgetByControlPoints::ACTIVITY_IN_WORK, 'msg' => 'К активности приступили' );
        }

        //Если в активности ничего не создано
        return array( 'status' => ActivitiesBudgetByControlPoints::ACTIVITY_NOT_START, 'msg' => 'Активность не начата' );
    }
}
