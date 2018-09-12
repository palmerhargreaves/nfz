<?php

/**
 * Description of AgreementSendModelMail
 *
 * @author Сергей
 */
class AgreementCancelReportMail extends HistoryMail
{
  function __construct(LogEntry $entry, User $user)
  {
    $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
    $text = 
<<<TEXT
        <p>
        Дилер "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) отменил отправку отчёта на согласование 
        "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>".
        </p>
TEXT;
        
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Отмена отправки отчёта на согласование',
        'text' => $text
      )
    );
  }
}
