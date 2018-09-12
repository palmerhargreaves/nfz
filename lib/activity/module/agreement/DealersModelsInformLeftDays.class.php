<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 20.10.2016
 * Time: 11:29
 */
class DealersModelsInformLeftDays
{
    const MODEL_10_DAYS_LEFT = 10;
    const MODEL_2_DAYS_LEFT = 2;

    private $_messages_list = array();
    private $_entries = array();

    public function __construct()
    {
        $this->_loadModelstoSendMessages();
    }

    private function _loadModelsToSendMessages() {
        $models_list = AgreementModelTable::getInstance()->createQuery('am')
            ->select('am.id as am_id, am.status as am_status, amr.status as amr_status, am.is_blocked, am.allow_use_blocked, am.use_blocked_to, values.*')
            ->leftJoin('am.Report amr')
            ->innerJoin('am.Values values')
            ->where('year(created_at) = ?', date('Y'))
            ->andWhere('am.status = ?', array('accepted'))
            ///->andWhere('am.is_blocked = ?', false)
            //->andWhere('am.status != ? or amr.status != ?', array('accepted', 'accepted'))
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $today = strtotime(date('d-m-Y H:i:s'));
        //$today = mktime(0,0,0,12,04,2016);
        foreach ($models_list as $model) {
            if ($model['am_id'] == 671) {
                //$today = mktime(0,0,0,12,04,2016);
            }
            //if ($model['am_status'] == 'accepted' && ((!is_null($model['Report']) && ($model['amr_status'] == 'accepted' || $model['amr_status'] == 'wait' || $model['amr_status'] == 'wait_specialist')) || is_null($model['Report']))) {
            if ($model['am_status'] == 'accepted' && ((!is_null($model['Report']) && ($model['amr_status'] == 'accepted' || $model['amr_status'] == 'wait' || $model['amr_status'] == 'wait_specialist')))) {
                continue;
            }

            if ($model['is_blocked'] && !$model['allow_use_blocked']) {
                continue;
            }

            /**Определяем количество дней до блокировки заявки */
            $model_period_end_value = AgreementModelValueTable::getPeriodValueFromModel($model);
            $model_period_end = strtotime(D::getNewDate($model_period_end_value, self::MODEL_10_DAYS_LEFT, '+', false, 'd-m-Y'));
            $elapsed_days = Utils::getElapsedTime($today - $model_period_end);

            /**
             * Если период размещения меньше тек. дня, проверяем на отклонение заявка, прибавляем 2 дня, получаем новую дату
             */
            if ($today > $model_period_end) {
                /**
                 * Если для заявки не загружен отчет, то блокируем заявку и если окончен период
                 */
                if (is_null($model['Report'])) {
                    /**
                     * Если заявка разблокирована, проверяем дату окончания разблокировки, в случае если период окончен, блокируем заявку
                    */

                    if ($model['is_blocked'] && !empty($model['use_blocked_to'])) {
                        $model_period_end = strtotime($model['use_blocked_to']);
                        $elapsed_days = Utils::getElapsedTime($today - $model_period_end);
                    } else {
                        $model_period_end = strtotime(D::getNewDate($model_period_end_value, self::MODEL_10_DAYS_LEFT + 4, '+', false, 'd-m-Y H:i:s'));
                        $elapsed_days = abs(Utils::getElapsedTime($today - $model_period_end));
                    }

                    if ($elapsed_days <= 2 && $elapsed_days > 0) {
                        $this->addDealerNotificationAndItem($model, AgreementModelsBlockInform::INFORM_STATUS_LEFT_2);
                        echo ('add_2_report_left'.$model['am_id']);
                    }
                    /**
                     * Если есть отчет и до выполнения осталось 0 дней блокируем заявку
                     */
                    else if ($elapsed_days == 0) {
                        $this->addDealerNotificationAndItem($model, AgreementModelsBlockInform::INFORM_STATUS_BLOCKED);

                        echo('add_blocked_report' . $model['am_id']);
                    }
                } else {
                    /**
                     * Делаем проверку после истечения срока размещения, если осталось 0 дней блокируем заявку и отправляем письмо
                     * Если завка была разблокирована учитываем дату разблокировки
                     */
                    if ($model['is_blocked'] && !empty($model['use_blocked_to'])) {
                        $model_period_end = $model['use_blocked_to'];
                    } else {
                        $log_entry = LogEntryTable::getInstance()->createQuery()->select('created_at')->where('object_id = ? and action = ? and object_type = ?', array($model['am_id'], 'declined', 'agreement_report'))->orderBy('id DESC')->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
                        if ($log_entry) {
                            $value = date('d-m-Y H:i:s', strtotime($log_entry['created_at']));
                            $model_period_end = D::getNewDate($value, 2, '+', false, 'd-m-Y H:i:s');
                        }
                    }

                    $model_period_end = strtotime($model_period_end);
                    $elapsed_days = Utils::getElapsedTime($today - $model_period_end);

                    /**
                     * Если есть отчет и осталось до выполнения 2 дня, информируем дилера о двух днях
                     */
                    if (!is_null($model['Report']) && $model['Report']['amr_status'] == 'declined' && $elapsed_days <= 2 && $elapsed_days > 0) {
                        $this->addDealerNotificationAndItem($model, AgreementModelsBlockInform::INFORM_STATUS_LEFT_2);

                        echo ('add_2_report'.$model['am_id']);
                    }
                    /**
                     * Если есть отчет и до выполнения осталось 0 дней блокируем заявку
                    */
                    else if (!is_null($model['Report']) && $model['Report']['amr_status'] == 'declined' && $elapsed_days == 0) {
                        $this->addDealerNotificationAndItem($model, AgreementModelsBlockInform::INFORM_STATUS_BLOCKED);
                        echo ('add_blocked_report'.$model['am_id']);
                    }
                }
            }
            /**
             * Если есть еще дни до окончания периода размещение получаем количество, определяем из полученного количества дней, сколько выходных, праздников
             */
            else if ($elapsed_days <= 0) {
                $elapsed_days = abs($elapsed_days);

                /*$elapsed_days += D::getNewDate($model_period_end, $elapsed_days, '-', true);*/
                if ($elapsed_days == 10) {
                    $this->addDealerNotificationAndItem($model, AgreementModelsBlockInform::INFORM_STATUS_LEFT_10);
                    echo ('add_10_report_left'.$model['am_id']);
                } else if ($elapsed_days <= 2 && $elapsed_days >= 0) {
                    /**
                     * Добавляем 2 дня к дате отклонения отчета, если равно 2 отправляем письмо
                     */
                    $log_entry = LogEntryTable::getInstance()->createQuery()->select('created_at')->where('object_id = ? and action = ? and object_type = ?', array($model['am_id'], 'declined', 'agreement_report'))->orderBy('id DESC')->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
                    if ($log_entry) {
                        $value = date('d-m-Y H:i:s', strtotime($log_entry['created_at']));
                    } else {
                        $value = $model_period_end_value;
                    }

                    $model_period_end = strtotime(D::getNewDate($value, self::MODEL_10_DAYS_LEFT + 2, '+', false, 'd-m-Y H:i:s'));
                    //$model_period_end = strtotime($value);
                    $elapsed_days = abs(Utils::getElapsedTime($today - $model_period_end));

                    //vaR_Dump('must_blocked '.$elapsed_days.'  '.$model['am_id']);
                    if ($elapsed_days <= 2 && $elapsed_days > 0) {
                        $this->addDealerNotificationAndItem($model, AgreementModelsBlockInform::INFORM_STATUS_LEFT_2);
                        echo ('add_2_report_left'.$model['am_id']);
                    }
                }
            }
        }
    }

    private function addDealerNotificationAndItem($model, $type, $send_email = true) {
        $allow_to_inform = false;

        if ($type == AgreementModelsBlockInform::INFORM_STATUS_BLOCKED) {
            $allow_to_inform = true;
        } else if (AgreementModelsBlockInformTable::getInstance()->createQuery()->where('model_id = ? and block_type = ?', array($model['am_id'], $type))->count() == 0) {
            $allow_to_inform = true;
        }

        if ($allow_to_inform) {
            if ($send_email) {
                $this->addDealerNotification(AgreementModelTable::getInstance()->find($model['am_id']), $type);
            }

            $this->addBlockItem($model['am_id'], $type);
        }
    }

    private function addBlockItem($model_id, $block_type) {
        $block_item = new AgreementModelsBlockInform();
        $block_item->setArray(
            array
            (
                'model_id' => $model_id,
                'block_type' => $block_type
            )
        );
        $block_item->save();
    }

    public function sendMessages() {
        foreach ($this->_entries as $entry) {
            sfContext::getInstance()->getMailer()->send($entry);
        }
    }

    protected function addDealerNotification(AgreementModel $model, $type)
    {
        $dealer_users = UserTable::getInstance()
            ->createQuery('u')
            ->innerJoin('u.DealerUsers du WITH dealer_id=?', $model->getDealerId())
            ->where('active=?', true)
            //->groupBy('du.dealer_id')
            ->execute();

        foreach ($dealer_users as $user) {
            if($user->getAllowReceiveMails()) {
                $this->addNotificationForUser($model, $user, $type);
            }
        }

        $user = UserTable::getInstance()->find(1);
        if ($user) {
            $this->addNotificationForUser($model, $user, $type);
        }

        $user = UserTable::getInstance()->find(939);
        if ($user) {
            $this->addNotificationForUser($model, $user, $type);
        }
    }

    protected function addNotificationForUser(AgreementModel $model, User $user, $type)
    {
        $message = new DealersModelsBlockInformLeftDays($user, $model, $type);
        $message->setPriority(1);

        $this->_entries[] = $message;

        /**
         * Утанавливаем параметры для заявки
         * Тип отправляемых писем
         * И если заявка блокируется устанавливаем соответсвующие параметры
        */
        $model->setBlockedInformStatus($type);
        if ($type == AgreementModelsBlockInform::INFORM_STATUS_BLOCKED) {
            $model->setIsBlocked(true);
            $model->setBlockedInform(2);
            $model->setAllowUseBlocked(false);
            $model->setUseBlockedTo('');
        }

        $model->save();

        //echo  $user->getEmail()." - ";
    }

}
