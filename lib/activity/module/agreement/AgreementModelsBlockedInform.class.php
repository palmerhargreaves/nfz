<?php

ini_set('memory_limit', '1000M');

/**
 * Description of AgreementModelsBlockedInform
 *
 */
class AgreementModelsBlockedInform
{
    private $_entries = array();

    //param 1 - week
    //param 2 - day

    public function sendMessages()
    {
        $models = AgreementModel::getBlockedModels();

        foreach ($models as $model) {
            $res = $model->isOutOfDate(-1, true);

            if (is_array($res)) {
                continue;
            }

            if (!$res) {
                $outOfDays = $model->getOutOfDays();

                if ($outOfDays <= 7 && $outOfDays > 1 && $model->getBlockedInform() == 0) {
                    $this->addDealerNotification($model, 'week');
                } else if ($outOfDays == 0) {
                    $this->addDealerNotification($model, 'day');
                }
            }
        }

        $this->send();
    }

    protected function addDealerNotification(AgreementModel $model, $type)
    {
        $dealer_users = UserTable::getInstance()
            ->createQuery('u')
            ->innerJoin('u.DealerUsers du WITH dealer_id=?', $model->getDealerId())
            ->where('active=?', true)
            ->groupBy('du.dealer_id')
            ->execute();

        foreach ($dealer_users as $user) {
            if($user->getAllowReceiveMails()) {
                $this->addNotificationForUser($model, $user, $type);
            }
        }
    }

    protected function addNotificationForUser(AgreementModel $model, User $user, $type)
    {
        $message = new AgreementDealerModelBlockInform($user, $model, $type);
        $message->setPriority(1);

        $this->_entries[] = $message;

        $model->setBlockedInform($type == 'week' ? 1 : 2);
        $model->save();
    }

    private function send()
    {
        echo "total models blocked: " . count($this->_entries) . "\r\n";

        foreach ($this->_entries as $entry) {
            sfContext::getInstance()->getMailer()->send($entry);
        }
    }

}
