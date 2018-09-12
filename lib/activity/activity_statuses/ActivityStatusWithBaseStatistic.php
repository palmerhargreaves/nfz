<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.07.2017
 * Time: 14:51
 */
class ActivityStatusWithBaseStatistic extends ActivityStatusBase
{
    public function getStatus()
    {
        $status = parent::getStatus();

        if ($status != ActivityModuleDescriptor::STATUS_NONE || !$this->dealer) {
            return $status;
        }

        if ($this->fields_values > 0) {
            if ($this->activity_models_created_count > 0) {

                return Utils::checkModelsCompleted($this->activity, $this->dealer, $this->year, $this->quarter)
                && $this->activity->isActivityStatisticComplete($this->dealer, null, true, $this->year, $this->quarter, $this->consider_activity_quarter ? array('check_by_quarter' => true) : null)
                    ? ActivityModuleDescriptor::STATUS_ACCEPTED
                    : ActivityModuleDescriptor::STATUS_WAIT_DEALER;
            } else {
                return ActivityModuleDescriptor::STATUS_NONE;
            }
        } else {
            //Если в активности есть созданные заявки
            //Получаем список выполненных заявок и делаем проверку на наличие в активности статистики
            //Если статистика есть и не заполнена возврашаем лож иначе активность вполнена
            if ($this->activity_models_created_count > 0) {
                $models_completed = $this->activity->getActivityCompletedModelsByParams($this->dealer->getId(), $this->year, $this->quarter);

                if (ActivityFieldsTable::getInstance()->createQuery()->where('activity_id = ?', $this->activity->getId())->count() > 0 && $models_completed > 0) {
                    return $this->activity->isActivityStatisticComplete($this->dealer, null, true, $this->year, $this->quarter, $this->consider_activity_quarter ? array('check_by_quarter' => true) : null)
                        ? ActivityModuleDescriptor::STATUS_ACCEPTED
                        : ActivityModuleDescriptor::STATUS_WAIT_DEALER;
                } else if ($models_completed > 0) {
                    return ActivityModuleDescriptor::STATUS_ACCEPTED;
                }

                //Если в активности есть заявки
                return ActivityModuleDescriptor::STATUS_WAIT_DEALER;
            }
        }

        return ActivityModuleDescriptor::STATUS_NONE;
    }
}
