<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class AgreementModelSentToSpecialistMail extends HistoryMail
{
  function __construct(LogEntry $entry, User $user, $message)
  {
    $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
    $text = $model->isConcept()
            ? $this->getConceptText($entry, $model)
            : $this->getModelText($entry, $model);
        
    if($message)
      $text .= '<p>'.nl2br($message).'</p>';
    
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Согласуйте '.($model->isConcept() ? 'макет' : 'концепцию'),
        'text' => $text
      )
    );
  }
  
  protected function getConceptText(LogEntry $entry, AgreementModel $model)
  {
    return
<<<TEXT
        <p>
        Вам для согласования отправлена <a href="{$this->getHistoryUrl($entry)}">концепция</a> дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}).
        </p>
TEXT;
  }
  
  protected function getModelText(LogEntry $entry, AgreementModel $model)
  {
    return
<<<TEXT
        <p>
        Вам для согласования отправлен макет "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>" дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}).
        </p>
TEXT;
  }
}
