<?php

/**
 * Description of DealersModelsBlockInformLeftDays
 *
 */
class DealersModelsBlockInformLeftDays extends TemplatedMail
{
    function __construct(User $user, AgreementModel $model, $by_type)
    {
        $site_url = sfConfig::get('app_site_url');

        $model_quarter = D::getQuarter(D::calcQuarterData($model->getCreatedAt()));
        if ($model->isModelCompleted()) {
            $model_quarter = D::getQuarter(Utils::getModelDateFromLogEntryWithYear($model->getId()));
        }

        $link = "<a href='" . $site_url . "/activity/" . $model->getActivityId() . "/module/agreement/models/model/" . $model->getId() . "/q/" . $model_quarter . "'>ссылка</a>";
        $text = $this->getText($model, $link, $by_type);

        $log_entry = new LogEntry();
        $log_entry->setArray(
            array
            (
                'user_id' => $user->getId(),
                'object_id' => $model->getId(),
                'description' => $text,
                'module_id' => 1,
                'action' => LogEntry::LOG_ACTION_DEALER_MODEL_BLOCK_INFORM,
                'object_type' => 'agreement_model',
                'login' => $user->getEmail(),
                'dealer_id' => $model->getDealerId(),
                'title' => 'Информирование о блокировке',
                'icon' => 'block_inform',
            )
        );
        $log_entry->save();

        parent::__construct(
            $user->getEmail(),
            //'kostig51@gmail.com',
            //'pklevtsova@palmerhargreaves.com',
            'global/mail_common_dealer',
            array(
                'user' => $user,
                'subject' => ($by_type == AgreementModelsBlockInform::INFORM_STATUS_LEFT_10 || $by_type == AgreementModelsBlockInform::INFORM_STATUS_LEFT_2)
                    ? 'Предупреждение о блокировке заявки'
                    : 'Заявка заблокирована',
                'text' => $text
            )
        );
    }

    protected function getText($model, $link, $by_type)
    {
        if ($by_type == AgreementModelsBlockInform::INFORM_STATUS_LEFT_10) {
            return $this->getTextBy10DaysLeft($model, $link);
        }
        else if ($by_type == AgreementModelsBlockInform::INFORM_STATUS_LEFT_2) {
            return $this->getTextBy2DaysLeft($model, $link);
        }
        else if ($by_type == AgreementModelsBlockInform::INFORM_STATUS_BLOCKED) {
            return $this->getBlockedText($model, $link);
        }
    }

    private function getTextBy10DaysLeft($model, $link)
    {
        return
        <<<TEXT
        <p>Ваша заявка {$model->getId()} «{$model->getName()}» по активности «{$model->getActivity()->getName()}» будет заблокирована через 10 рабочих дней.</p>
<p>Вам необходимо подгрузить корректные отчетные документы по итогам размещения утвержденного материала.</p>
<p>Перейти в заявку ({$link})</p>

TEXT;
    }

    private function getTextBy2DaysLeft($model, $link)
    {
        return
        <<<TEXT
        <p>Ваша заявка {$model->getId()} «{$model->getName()}» по активности «{$model->getActivity()->getName()}» будет заблокирована через 2 рабочих дня.</p>
<p>Вам необходимо подгрузить корректные отчетные документы по итогам размещения утвержденного материала.</p>
<p>Перейти в заявку ({$link})</p>

TEXT;
    }

    private function getBlockedText($model, $link)
    {
        return
            <<<TEXT
<p>Ваша заявка {$model->getId()} «{$model->getName()}» по активности «{$model->getActivity()->getName()}» заблокирована.<p>
<p>Перейти в заявку ({$link})</p>
TEXT;
    }
}
