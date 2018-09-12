<?php
/**
 * Created by PhpStorm.
 * User: kostig51
 * Date: 16.02.2018
 * Time: 13:41
 */

class ActivityStatisticPreCheckByUser extends ActivityStatisticPreCheckAbstract {
    public function completeStatistic ( $result, $statistic )
    {
        $user = $this->_my_user->getAuthUser();

        $curr_quarter = !empty($this->_request_q) && $this->_request_q != 0 ? $this->_request_q : D::getQuarter(D::calcQuarterData(time()));
        $curr_year = $this->_my_user->getCurrentYear() != 0 ? $this->_my_user->getCurrentYear() : D::getYear(D::calcQuarterData(date('d-m-Y')));

        //Делаем проверку на наличие
        $pre_check_status = ActivityStatisticPreCheckTable::getInstance()->createQuery()
            ->where('activity_id = ?', $this->_activity->getId())
            ->andWhere('statistic_id = ?', $this->_activity->getActivityVideoStatistics()->getFirst()->getId())
            ->andWhere('dealer_id = ?', $user->getDealer()->getId())
            ->andWhere('quarter = ?', $curr_quarter)
            ->andWhere('year = ?', $curr_year)
            ->fetchOne();

        if (!$pre_check_status) {
            $pre_check_status = new ActivityStatisticPreCheck();

            $pre_check_status->setArray(array(
                'activity_id' => $this->_activity->getId(),
                'statistic_id' => $this->_activity->getActivityVideoStatistics()->getFirst()->getId(),
                'quarter' => $curr_quarter,
                'year' => $curr_year,
            ));
        }

        $pre_check_status->setDealerId($user->getDealer()->getId());

        //После каждой отправки отмечаем статистику по активности как ожидающую
        $pre_check_status->setIsChecked(self::CHECK_STATUS_IN_PROGRESS);

        //Убираем пользователя который сделал проверку
        $pre_check_status->setUserWhoCheck(0);

        $pre_check_status->save();

        //Делаем рассылку системным пользователям
        $users = $this->getMailUsers();
        foreach ($users as $to_user) {
            $message = new ActivityStatisticCheckByUserMail($user, $to_user, $this->_activity, $curr_quarter, $curr_year);
            $message->setPriority(1);
            sfContext::getInstance()->getMailer()->send($message);
        }
    }

    public function status($activity, $user, $quarter, $year) {
        $data_to_check = $this->getStatisticData($activity, $user, $quarter, $year);
        if ($data_to_check) {
            return $data_to_check ? $data_to_check->getIsChecked() : self::CHECK_STATUS_NONE;
        }

        return self::CHECK_STATUS_NONE;
    }

    public function accept($activity, $user, $quarter, $year) {
        $data_to_check = $this->getStatisticData($activity, $user, $quarter, $year);

        if ($data_to_check) {
            $data_to_check->setIsChecked(self::CHECK_STATUS_CHECKED);
            $data_to_check->setUserWhoCheck($user->getId());
            $data_to_check->save();

            //Выполнение статистики
            $this->_activity = $activity;

            $new_entry = new LogEntry();
            $new_entry->setArray(
                array
                (
                    'user_id' => $user->getId(),
                    'description' => 'Согласование данных по статистике сис. пользователем.',
                    'object_id' => $activity->getId(),
                    'module_id' => 1,
                    'action' => 'activity_statistic_data_accept',
                    'object_type' => 'activity_statistic',
                    'login' => $user->getEmail(),
                    'dealer_id' => $data_to_check->getDealerId(),
                    'title' => 'Согласование статистики',
                )
            );
            $new_entry->save();

            $query = ActivityDealerStaticticStatusTable::getInstance()->createQuery()->where('dealer_id = ? and activity_id = ? and year = ?',
                array
                (
                    $data_to_check->getDealerId(),
                    $activity->getId(),
                    $data_to_check->getYear()
                )
            );

            $quarter = 'q'.$data_to_check->getQuarter();

            //Фиксируем выполнение статистике в БД
            $item = $query->fetchOne();
            if (!$item) {
                $item = new ActivityDealerStaticticStatus();
                $item->setArray(
                    array
                    (
                        'dealer_id' => $user->getDealer()->getId(),
                        'activity_id' => $activity->getId(),
                        'ignore_q' . $data_to_check->getQuarter() . '_statistic' => 0,
                        'stat_type' => Activity::ACTIVITY_STATISTIC_TYPE_SIMPLE,
                        $quarter => $data_to_check->getQuarter(),
                        'year' => $year,
                        'complete' => true
                    )
                );
            } else {
                $item->setArray(
                    array
                    (
                        'ignore_q' .  $data_to_check->getQuarter() . '_statistic' => 0,
                        $quarter =>  $data_to_check->getQuarter(),
                        'complete' => true,
                        'year' => $year
                    )
                );
            }
            $item->save();

            $users_mails = DealerUserTable::getInstance()->createQuery()->where('dealer_id = ?', $data_to_check->getDealerId())->execute();
            foreach ($users_mails as $user_mail) {
                $message = new ActivityStatisticCheckByUserAcceptMail($user_mail->getUser(), $activity, $quarter, $year);
                $message->setPriority(1);
                sfContext::getInstance()->getMailer()->send($message);
            }

            return true;
        }

        return false;
    }

    public function cancel($activity, $user, $quarter, $year) {
        $data_to_check = $this->getStatisticData($activity, $user, $quarter, $year);

        if ($data_to_check) {
            $data_to_check->setIsChecked(self::CHECK_STATUS_CANCEL);
            $data_to_check->setUserWhoCheck($user->getId());
            $data_to_check->save();

            $new_entry = new LogEntry();
            $new_entry->setArray(
                array
                (
                    'user_id' => $user->getId(),
                    'description' => 'Отклонение данных по согласованию статистики сис. пользователем.',
                    'object_id' => $activity->getId(),
                    'module_id' => 1,
                    'action' => 'activity_statistic_data_cancel',
                    'object_type' => 'activity_statistic',
                    'login' => $user->getEmail(),
                    'dealer_id' => $data_to_check->getDealerId(),
                    'title' => 'Согласование статистики',
                )
            );
            $new_entry->save();

            $users_mails = DealerUserTable::getInstance()->createQuery()->where('dealer_id = ?', $data_to_check->getDealerId())->execute();
            foreach ($users_mails as $user_mail) {
                $message = new ActivityStatisticCheckByUserCancelMail($user_mail->getUser(), $activity, $quarter, $year);
                $message->setPriority(1);
                sfContext::getInstance()->getMailer()->send($message);
            }

            return true;
        }

        return false;
    }

    private function getStatisticData($activity, $user, $quarter, $year) {
        $statistic = $activity->getActivityVideoStatistics()->getFirst();

        //Проверка
        return ActivityStatisticPreCheckTable::getInstance()->createQuery()
            ->where('activity_id = ?', $activity->getId())
            ->andWhere('statistic_id = ?', $statistic->getId())
            ->andWhere('dealer_id = ?', $user->getDealer()->getId())
            ->andWhere('quarter = ?', $quarter)
            ->andWhere('year = ?', $year)
            ->fetchOne();
    }
}
