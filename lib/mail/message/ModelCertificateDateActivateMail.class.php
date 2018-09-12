<?php

/**
 */
class ModelCertificateDateActivateMail extends TemplatedMail
{
  function __construct(Dealer $user)
  {
    $message = "";
    
    //$link = sprintf('%s/news/%s', sfConfig::get('app_site_url'), $data->getId());
    //$message .= '<br /><a class="li-anchor" href="'.$link.'">перейти</a>';

    parent::__construct(
      //$user->getEmail(), 
    	'kostig51@gmail.com',
    	//'emonakova@palmerhargreaves.com',
        'global/mail_common', 
        array(
            'user' => $user,
            'subject' => 'Сервисные акции',
            'text' => 
<<<TEXT
        {$message}
TEXT
      )
    );
  }
}
