<?php

/**
 * Description of AgreementReportAcceptedMail
 *
 * @author Сергей
 */
class AgreementReportAcceptedMail extends HistoryMail
{
    function __construct(LogEntry $entry, User $user)
    {
        $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
        $report = $model->getReport();
        $text =
            <<<TEXT
                                <p>
        Отчёт "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>" дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) согласован!
        </p>
TEXT;

        if ($report->getAgreementComments())
            $text .= '<p>' . nl2br($report->getAgreementComments()) . '</p>';

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Отчёт согласован',
                'text' => $text
            )
        );
    }
}
