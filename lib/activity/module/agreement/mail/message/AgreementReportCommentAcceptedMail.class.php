<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class AgreementReportCommentAcceptedMail extends HistoryMail
{
    function __construct ( LogEntry $entry, User $user, $params )
    {
        $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
        $specialist = $params[ 'specialist' ];
        $text =
            <<<TEXT
        <p>
        Отчёт дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>" согласован
        специалистом ({$specialist->getGroup()->getName()}, {$specialist->selectName()})
        </p>
TEXT;

        if ($params[ 'comment' ])
            $text .= '<p>' . nl2br($params[ 'comment' ]) . '</p>';

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Отчёт согласован специалистом',
                'text' => $text
            )
        );
    }
}
