<?php

/**
 */
class NewsMail extends TemplatedMail
{
  function __construct(Dealer $user, $data)
  {
  	$message = 'Добавлена нoвость:<br>
				<table><tr><td valign="top">';

	$message .= '</td><td  valign="top">';
	$message .= "<b>".$data->getName()."</b><br />";
	
	$message .= strip_tags($data->getText());
	$message .= '<br />';

	$link = sprintf('%s/news/%s', sfConfig::get('app_site_url'), $data->getId());
	//$message .= '<br /><a class="li-anchor" href="'.$link.'">перейти</a>';

	$message .= '</td></tr>
				</table><br />';

    parent::__construct(
      //$user->getEmail(), 
    	//'kostig51@gmail.com',
    	'emonakova@palmerhargreaves.com',
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Новости SP2',
        'text' => 
<<<TEXT
        {$message}
TEXT
      )
    );
  }
}
