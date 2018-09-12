<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class AgreementReportCommentDeclinedMail extends HistoryMail
{
  function __construct(LogEntry $entry, User $user, $params)
  {
    $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
    $specialist = $params['specialist'];
    $text = 
<<<TEXT
        <p>
        Отчёт дилера "{$model->getDealer()->getName()}" ({$model->getDealer()->getNumber()}) "<a href="{$this->getHistoryUrl($entry)}">{$model->getName()}</a>" отклонён
        специалистом ({$specialist->getGroup()->getName()}, {$specialist->selectName()}).
        </p>
TEXT;
        
    if($params['comment'])
      $text .= '<p>'.nl2br($params['comment']).'</p>';
    
    if($params['comment_file'])
      $text .= '<p><a href="'.sfConfig::get('app_site_url').'/uploads/'.  MessageFile::FILE_PATH.'/'.$params['comment_file'].'">Скачать файл с комментариями</a></p>';
      
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Отчёт отклонён специалистом',
        'text' => $text
      )
    );
  }
}
