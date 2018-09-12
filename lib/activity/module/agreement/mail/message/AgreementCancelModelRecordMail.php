<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 27.08.2016
 * Time: 10:30
 */

class AgreementCancelModelRecordMail extends HistoryMail
{
    function __construct(LogEntry $entry, User $user)
    {
        $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
        $type = 'записи';
        $text =
            <<<TEXT
                    <p>
        Дилер "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) отменил отправку $type на согласование
        "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>".
        </p>
TEXT;

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => "Отмена отправки $type на согласование",
                'text' => $text
            )
        );
    }
}
