<?php

/**
 * Description of ActivityCreateMail
 *
 */
class ActivityCreateMail extends TemplatedMail
{
  function __construct(User $user, Activity $activity, $isNew = true)
  {
    $site_url = sfConfig::get('app_site_url');
    
    $subject = sprintf('Добавлена новая активность %s', $activity->getName());
    if(!$isNew)
      $subject = sprintf('Изменение параметров активности %s', $activity->getName());

    $link = "<a href=".$site_url."/activity/".$activity->getId().">перейти</a>";
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => $subject,
        'text' => 
<<<TEXT
        <p>
        Уважаемый дилер!<br/>
        На портале dm.vw-servicepool.ru добавлена новая обязательная активность <strong>"{$activity->getName()}"</strong> ({$link}).
        <br/>
        Можете приступать к согласованию материалов.
        <br/>
        ----------------------------------
        <br/>
        </p>
TEXT
      )
    );
  }
}
