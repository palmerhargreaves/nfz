<?php
/**
 * Created by PhpStorm.
 * User: kostig51
 * Date: 16.02.2018
 * Time: 13:45
 */

class ActivityStatisticPreCheckAbstract implements ActivityStatisticPreCheckInterface {
    protected $_activity = null;
    protected $_request = null;
    protected $_my_user = null;
    protected $_files = array();
    protected $_to_importer = false;

    protected static $_instance = null;

    const CHECK_STATUS_NONE = 0;
    const CHECK_STATUS_CHECKED = 1;
    const CHECK_STATUS_IN_PROGRESS = 2;
    const CHECK_STATUS_CANCEL = 3;

    public function __construct ()
    {

    }

    /**
     * Сохраняем данные полей статистики
     * @return mixed
     */
    public function saveData (sfWebRequest $request, $my_user, $files, $to_importer = false, $activity = null)
    {
        $this->_activity = $activity;
        $this->_request = $request;
        $this->_my_user = $my_user;
        $this->_files = $files;
        $this->_to_importer = $to_importer;

        $items = json_decode($this->_request->getParameter('txt_frm_fields_data'));

        $user = $this->_my_user->getAuthUser();
        $result = array( 'success' => false, 'msg' => '' );
        foreach ($items as $key => $data) {
            $result[ 'success' ] = ActivityFields::saveFieldData($this->_request, $data->id, $data->value, $this->_my_user);
        }

        $allowed_file_types = array( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats' );
        if (!empty($files)) {
            foreach ($files as $file_key => $file_data) {
                if (isset($file_data[ 'name' ]) && !empty($file_data[ 'name' ])) {
                    if (in_array($file_data[ 'type' ], $allowed_file_types)) {

                        $file_key_exploded = explode('_', $file_key);
                        $file_key_data = $file_key_exploded[ count($file_key_exploded) - 1 ];

                        $uniq_model = new UniqueFileNameGenerator(sfConfig::get('app_uploads_path') . ActivityFields::FIELD_FILE_PATH);
                        $gen_file_name = $uniq_model->generate($file_data[ 'name' ]);

                        if (move_uploaded_file($file_data[ 'tmp_name' ], sfConfig::get('app_uploads_path') . ActivityFields::FIELD_FILE_PATH . '/' . $gen_file_name)) {
                            ActivityFields::saveFieldData($this->_request, $file_key_data, $gen_file_name, $this->_my_user);
                        }
                    } else {
                        $result[ 'success' ] = false;
                        $result[ 'msg' ] = sprintF('%s (%s).', 'Неверный формат файла ', $file_data[ 'type' ]);
                    }
                }
            }
        }

        //Если для активности разрешено отправлять нескольько раз данные на согласование, фиксируем дату последних изменений в данных
        if ($this->_activity && $this->_activity->isVideoRecordStatisticsActive()) {
            $statistic = $this->_activity->getActivityVideoStatistics()->getFirst();
        }

        //Если для активности разрешено отправлять нескольько раз данные на согласование, фиксируем дату последних изменений в данных
        if ($statistic && $statistic->getNotUsingImporter()) {
            $last_update_activity = new ActivityStatisticLastUpdates();
            $last_update_activity->setArray(
                array(
                    'activity_id' => $this->_activity->getId(),
                    'statistic_id' => $statistic->getId(),
                    'dealer_id' => $user->getDealer()->getId(),
                    //'created_at' => date('Y-m-d H:i:s')
                )
            );
            $last_update_activity->save();
        }

        //Сохраняем статус выполнения статистики
        $this->completeStatistic($result, $statistic);

        return $result;
    }

    /**
     * Отмечаем активность выполненной
     * @param $result
     * @param $statistic
     * @return mixed
     */
    public function completeStatistic ($result, $statistic)
    {
        if (!$result[ 'success' ]) {
            return;
        }

        //Если не перадается статистика, получаем первыую по умолчанию
        if (!$statistic) {
            $statistic = $this->_activity->getActivityVideoStatistics()->getFirst();
        }

        $user = $this->_my_user->getAuthUser();
        $year = $this->_my_user->getCurrentYear() != 0 ? $this->_my_user->getCurrentYear() : D::getYear(D::calcQuarterData(date('d-m-Y')));

        $query = ActivityDealerStaticticStatusTable::getInstance()->createQuery()->where('dealer_id = ? and activity_id = ? and year = ?',
            array
            (
                $user->getDealer()->getId(),
                $this->_request->getParameter('activity'),
                $year
            )
        );

        $this->_request_q = $this->_request->getParameter('quarter');
        $this->_request_q = !empty($this->_request_q) && $this->_request_q != 0 ? $this->_request_q : D::getQuarter(D::calcQuarterData(time()));

        $curr_quarter = $this->_my_user->getCurrentQuarter() != 0 ? $this->_my_user->getCurrentQuarter() : $this->_request_q;
        $quarter = 'q' . $curr_quarter;

        $item = $query->fetchOne();
        if (!$item) {
            $item = new ActivityDealerStaticticStatus();
            $item->setArray(
                array
                (
                    'dealer_id' => $user->getDealer()->getId(),
                    'activity_id' => $this->_request->getParameter('activity'),
                    'ignore_q' . $curr_quarter . '_statistic' => $statistic && $statistic->getNotUsingImporter() ? false : !$this->_to_importer,
                    'stat_type' => Activity::ACTIVITY_STATISTIC_TYPE_SIMPLE,
                    $quarter => $curr_quarter,
                    'year' => $year,
                    'complete' => true
                )
            );
        } else {
            $item->setArray(
                array
                (
                    'ignore_q' . $curr_quarter . '_statistic' => $statistic && $statistic->getNotUsingImporter() ? false : !$this->_to_importer,
                    $quarter => $curr_quarter,
                    'complete' => true,
                    'year' => $year
                )
            );
        }

        $item->save();
    }

    public function status($activity, $user, $quarter, $year) {
        return self::CHECK_STATUS_NONE;
    }

    /**
     * Принять данные по статистике
     * @param $user
     * @param $quarter
     * @param $year
     * @return mixed
     */
    public function accept ( $activity, $user, $quarter, $year )
    {
        // TODO: Implement accept() method.
    }

    /**
     * Отменить данные по статистике
     * @param $user
     * @param $quarter
     * @param $year
     * @return mixed
     */
    public function cancel ( $activity, $user, $quarter, $year )
    {
        // TODO: Implement cancel() method.
    }

    /**
     * Получить список системных пользователей для рассылки
     */
    protected function getMailUsers() {
        $users = array();

        $items = ActivityStatisticPreCheckUsersTable::getInstance()->createQuery()->execute();
        foreach ($items as $item) {
            $users[] = $item->getUser();
        }

        return $users;
    }
}
