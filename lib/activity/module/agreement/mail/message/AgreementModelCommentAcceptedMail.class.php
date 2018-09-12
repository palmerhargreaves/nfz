<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class AgreementModelCommentAcceptedMail extends HistoryMail
{
  function __construct(LogEntry $entry, User $user, $params)
  {
    $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
    $type = $model->isConcept() ? 'Концепция' : 'Макет';
    $ending = $model->isConcept() ? 'а' : '';
    $specialist = $params['specialist'];
    $text = $model->isConcept() 
            ? $this->getConceptText($entry, $specialist, $model)
            : $this->getModelText($entry, $specialist, $model);
        
    if($params['comment'])
      $text .= '<p>'.nl2br($params['comment']).'</p>';
    
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => "$type согласован{$ending} специалистом",
        'text' => $text
      )
    );
  }
  
  protected function getConceptText(LogEntry $entry, User $specialist, AgreementModel $model)
  {
    return 
<<<TEXT
        <p>
        <a href="{$this->getHistoryUrl($entry)}">Концепция</a> дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) согласована
        специалистом ({$specialist->getGroup()->getName()}, {$specialist->selectName()})
        </p>
TEXT;
  }
  
  protected function getModelText(LogEntry $entry, User $specialist, AgreementModel $model)
  {
    return 
<<<TEXT
        <p>
        Макет дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>" согласован
        специалистом ({$specialist->getGroup()->getName()}, {$specialist->selectName()})
        </p>
TEXT;
  }
}
