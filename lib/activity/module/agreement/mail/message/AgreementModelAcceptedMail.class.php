<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class AgreementModelAcceptedMail extends HistoryMail
{
    function __construct(LogEntry $entry, User $user)
    {
        $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
        $type = $model->isConcept() ? 'Концепция' : 'Макет';
        $ending = $model->isConcept() ? 'а' : '';
        $text = $model->isConcept()
            ? $this->getConceptText($entry, $model)
            : $this->getModelText($entry, $model);

        if ($model->getManagerStatus() == 'declined' && $model->getDesignerStatus() == 'wait') {
            $label = "Внесите коментарии менеджера. ";
        } else if ($model->getDesignerStatus() == 'declined') {
            $label = "Внесите коментарии дизайнера. ";
        } else if ($model->getManagerStatus() == 'wait' && $model->getDesignerStatus() == 'wait') {
            $label = "Внесите коментарии менеджера. ";
        }

        if ($model->getAgreementComments()) {
            $label .= nl2br($model->getAgreementComments());
        }
        $text .= '<p>' . $label . '</p>';

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => "$type согласован{$ending}",
                'text' => $text
            )
        );
    }

    protected function getModelText(LogEntry $entry, AgreementModel $model)
    {
        return
            <<<TEXT
                    <p>Макет № {$model->getId()} «<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>» согласован!</p>
TEXT;
    }

    protected function getConceptText(LogEntry $entry, AgreementModel $model)
    {
        return
            <<<TEXT
                    <p>Концепция № {$model->getId()} «<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>» согласована!</p>
TEXT;
    }
}
