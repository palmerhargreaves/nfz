<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class AgreementModelCommentDeclinedMail extends HistoryMail
{
  function __construct(LogEntry $entry, User $user, $params)
  {
    $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
    $specialist = $params['specialist'];
    $type = $model->isConcept() ? 'Концепция' : 'Макет';
    $ending = $model->isConcept() ? 'а' : '';
    $text = $model->isConcept()
            ? $this->getConceptText($entry, $specialist, $model)
            : $this->getModelText($entry, $specialist, $model);
        
    if($params['comment'])
      $text .= '<p>'.nl2br($params['comment']).'</p>';
    
    if($params['comment_file'])
      $text .= '<p><a href="'.sfConfig::get('app_site_url').'/uploads/'.  MessageFile::FILE_PATH.'/'.$params['comment_file'].'">Скачать файл с комментариями</a></p>';
      
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => "$type отклонен{$ending} специалистом",
        'text' => $text
      )
    );
  }
  
  protected function getConceptText(LogEntry $entry, User $specialist, AgreementModel $model)
  {
    return 
<<<TEXT
        <p>
        <a href="{$this->getHistoryUrl($entry)}">Концепция</a> дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) отклонёна
        специалистом ({$specialist->getGroup()->getName()}, {$specialist->selectName()}).
        </p>
TEXT;
  }
  
  protected function getModelText(LogEntry $entry, User $specialist, AgreementModel $model)
  {
    return 
<<<TEXT
        <p>
        Макет дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>" отклонён
        специалистом ({$specialist->getGroup()->getName()}, {$specialist->selectName()}).
        </p>
TEXT;
  }
}
