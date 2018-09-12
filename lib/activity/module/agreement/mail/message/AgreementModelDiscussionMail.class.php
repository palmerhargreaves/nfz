<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class AgreementModelDiscussionMail extends DiscussionMail
{
  function __construct(User $user, $entries)
  {
    $site_url = sfConfig::get('app_site_url');
    
    $discussion_messages = array();
    
    foreach($entries as $entry)
    {
      $sender = $entry->getUser();
      $message = $this->getMessage($entry);
      if(!$message)
        continue;
      
      $text = nl2br(strip_tags($message->getText()));
      
      $date = date('d.m.Y H:i', D::toUnix($message->created_at));
      
      $dealer = $entry->getDealer() ? $entry->getDealer()->getName() : '';
      
      $discussion = $message->getDiscussion();
      if(!$discussion)
        continue;
      
      $model = $discussion->getModels()->offsetGet(0);
      if(!$model)
        continue;
      
      $activity = $model->getActivity();
      if(!$activity)
        continue;
      
      $model_description = $activity->getName().' / '.$model->getName();
      
      $discussion_messages[] = <<<TEXT
        <p>
          <small>{$dealer}</small>
          <br>
          <small>{$model_description}</small>
          <br>
          Заявка №{$model->getId()}
          <br>
          <i>$date</i>
          <b>{$sender->selectName()}:</b>
          <br>
          {$text}
          <div>{$this->getFiles($message)}</div>
          <br>
          <a href="{$site_url}/history/{$entry->getId()}">перейти к сообщению</a>
        </p>
TEXT;
    }
    
    $discussion_messages = implode('', $discussion_messages);
    
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Новые сообщения',
        'text' => 
<<<TEXT
        <p>Поступили новые сообщения:</p>
        {$discussion_messages}
TEXT
      )
    );
  }
}
