<?php
/**
 * Created by PhpStorm.
 * User: kostig51
 * Date: 16.02.2018
 * Time: 13:51
 */

class ActivityStatisticCheckFactory {
    private static $_instance = null;
    private static $_activity = null;

    public static function getInstance($activity) {
        if (is_null(self::$_instance)) {
            self::$_instance = new ActivityStatisticCheckFactory();
        }

        self::$_activity = $activity;

        return self::$_instance;
    }

    /**
     * Сохраняем данные по статистике с учетом установленного флага, немедленное выполнение или на проверку админу
     * @param $request
     * @param $user
     * @param $_FILES
     * @param $to_importer
     * @return mixed
     */
    public function save($request, $user, $_FILES, $to_importer) {
        return $this->getClass()->saveData($request, $user, $_FILES, $to_importer, self::$_activity);
    }

    /**
     * Получить статус проверки данных статистики администрацией
     * @param $user
     * @param $quarter
     * @param $year
     * @return mixed
     */
    public function status($user, $quarter, $year) {
        return $this->getClass()->status(self::$_activity, $user, $quarter, $year);
    }

    /**
     * Отменить данные по статистике
     * @param $user
     * @param $quarter
     * @param $year
     * @return mixed
     */
    public function cancel($user, $quarter, $year) {
        return $this->getClass()->cancel(self::$_activity, $user, $quarter, $year);
    }

    /**
     * Принять данные по статистике
     * @param $user
     * @param $quarter
     * @param $year
     * @return mixed
     */
    public function accept($user, $quarter, $year) {
        return $this->getClass()->accept(self::$_activity, $user, $quarter, $year);
    }

    /**
     * Получить класс
     * @return string
     */
    private function getClass() {
        $cls = 'ActivityStatisticCheck';
        if (self::$_activity->getActivityVideoStatistics()->count() && self::$_activity->getActivityVideoStatistics()->getFirst()->getAllowStatisticPreCheck()) {
            $cls = 'ActivityStatisticPreCheckByUser';
        }

        return new $cls();
    }
}
