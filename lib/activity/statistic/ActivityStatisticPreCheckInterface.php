<?php
/**
 * Created by PhpStorm.
 * User: kostig51
 * Date: 16.02.2018
 * Time: 13:41
 */

interface ActivityStatisticPreCheckInterface {
    /**
     * Сохраняем данные полей статистики
     * @return mixed
     */
    public function saveData(sfWebRequest $request, $my_user, $files, $to_importer = false, $activity = null);

    /**
     * Отмечаем активность выполненной
     * @param $result
     * @param $statistic
     * @return mixed
     */
    public function completeStatistic($result, $statistic);

    /**
     * Получить статус проверки данных статистики администрацией
     * @param $user
     * @param $quarter
     * @param $year
     * @return mixed
     */
    public function status($activity, $user, $quarter, $year);

    /**
     * Принять данные по статистике
     * @param $user
     * @param $quarter
     * @param $year
     * @return mixed
     */
    public function accept($activity, $user, $quarter, $year);

    /**
     * Отменить данные по статистике
     * @param $user
     * @param $quarter
     * @param $year
     * @return mixed
     */
    public function cancel($activity, $user, $quarter, $year);
}