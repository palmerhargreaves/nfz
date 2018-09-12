<?php

/**
 * Description of AgreementSendReportMail
 *
 * @author Сергей
 */
class AgreementSendReportMail extends HistoryMail
{
  function __construct(LogEntry $entry, User $user)
  {
    $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
    $text = $model->isConcept()
            ? $this->getConceptText($entry, $model)
            : $this->getModelText($entry, $model);
        
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Согласование отчёта',
        'text' => $text
      )
    );
  }
  
  protected function getConceptText(LogEntry $entry, AgreementModel $model)
  {
    return
<<<TEXT
        <p>
        Дилер "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) отправил на согласование отчёт
        по <a href="{$this->getHistoryUrl($entry)}">концепции</a>.
        </p>
TEXT;
  }
  
  protected function getModelText(LogEntry $entry, AgreementModel $model)
  {
    return
<<<TEXT
        <p>
        Дилер "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) отправил на согласование отчёт
        "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>".
        </p>
TEXT;
  }
}
