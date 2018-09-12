<?php

/**
 * Description of RecoveryPasswordMail
 *
 * @author Сергей
 */
class RecoveryPasswordMail extends TemplatedMail
{
  function __construct(User $user)
  {
    $site_url = sfConfig::get('app_site_url');
    $recovery_url = "$site_url/recovery_password/newPassword?id={$user->getId()}&key={$user->getRecoveryKey()}";
    
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Восстановление пароля',
        'text' => 
<<<TEXT
        <p>
        Чтобы восстановить пароль, перейдите по следующей ссылке: <a href="$recovery_url">$recovery_url</a>
        </p>
TEXT
      )
    );
  }
}
