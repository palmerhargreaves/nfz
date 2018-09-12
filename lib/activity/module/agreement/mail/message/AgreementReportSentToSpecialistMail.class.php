<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class AgreementReportSentToSpecialistMail extends HistoryMail
{
    function __construct(LogEntry $entry, User $user, $message)
    {
        $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
        $text =
            <<<TEXT
                    <p>
        Вам для согласования отправлен отчёт 
        дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()})
        "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>".
        </p>
TEXT;

        if ($message)
            $text .= '<p>' . nl2br($message) . '</p>';

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Согласуйте отчёт',
                'text' => $text
            )
        );
    }
}
