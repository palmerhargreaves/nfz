<?php

/**
 * Description of AgreementSendModelMail
 *
 * @author Сергей
 */
class AgreementSendModelMail extends HistoryMail
{
    function __construct(LogEntry $entry, User $user)
    {
        $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
        $text = $model->isConcept()
            ? $this->getConcepText($entry, $model)
            : $this->getModelText($entry, $model);

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Согласование ' . ($model->isConcept() ? 'концепции' : 'макета'),
                'text' => $text
            )
        );
    }

    protected function getConcepText(LogEntry $entry, AgreementModel $model)
    {
        return
            <<<TEXT
                    <p>
        Дилер "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) отправил на согласование 
        <a href="{$this->getHistoryUrl($entry)}">концепцию</a>.
        </p>
TEXT;
    }

    protected function getModelText(LogEntry $entry, AgreementModel $model)
    {
        return
            <<<TEXT
                    <p>
        Дилер "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) отправил на согласование макет
        "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>".
        </p>
TEXT;
    }
}
