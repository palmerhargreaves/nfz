<?php

/**
 * Description of AgreementReportAcceptedMail
 *
 * @author Сергей
 */
class AgreementReportDeclinedMail extends HistoryMail
{
    function __construct(LogEntry $entry, User $user)
    {
        $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
        $report = $model->getReport();
        $reason = $model->getDeclineReason() && $model->getDeclineReason()->getName()
            ? 'по следующей причине: "' . $model->getDeclineReason()->getName() . '"'
            : '';

        $text = $this->getText($entry, $model);

        if($model->getManagerStatus() == 'declined' && $model->getDesignerStatus() == 'wait') {
            $label = "Внесите коментарии менеджера. ";
        }
        else if ($model->getDesignerStatus() == 'declined') {
            $label = "Внесите коментарии дизайнера. ";
        } else if ($model->getManagerStatus() == 'wait' && $model->getDesignerStatus() == 'wait') {
            $label = "Внесите коментарии менеджера. ";
        }

        if ($report->getAgreementComments()) {
            $label .= '<p>' . nl2br($report->getAgreementComments()) . '</p>';
        }
        $text .= '<p>'.$label.'</p>';

        if ($report->getAgreementCommentsFile()) {
            $text .= '<p><a href="' . sfConfig::get('app_site_url') . '/uploads/' . AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH . '/' . $report->getAgreementCommentsFile() . '">Скачать файл с комментариями</a></p>';
        }

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Отчёт отклонён',
                'text' => $text
            )
        );
    }

    private function getText($entry, $model) {
        return <<<TEXT
        <p>Отчет по заявке № {$model->getId()} "{$model->getName()}" <a href="{$this->getHistoryUrl($entry)}">отклонен.</a></p>
TEXT;
    }
}
