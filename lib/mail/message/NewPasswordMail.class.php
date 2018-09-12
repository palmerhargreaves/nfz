<?php

/**
 * Description of NewPasswordMail
 *
 * @author Сергей
 */
class NewPasswordMail extends TemplatedMail
{
  function __construct(User $user, $password)
  {
    parent::__construct(
      $user->getEmail(), 
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Восстановление пароля',
        'text' => 
<<<TEXT
        <p>
        Ваш новый пароль: $password
        </p>
TEXT
      )
    );
  }
}
