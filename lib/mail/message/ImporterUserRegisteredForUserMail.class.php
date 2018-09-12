<?php

/**
 * Description of DealerUserRegisteredForUserMail
 *
 * @author Сергей
 */
class ImporterUserRegisteredForUserMail extends TemplatedMail
{
  function __construct(User $user)
  {
    parent::__construct(
        //'emonakova@palmerhargreaves.com',
      $user->getEmail(),
      'global/mail_common', 
      array(
        'user' => $user,
        'subject' => 'Регистрация',
        'text' => 
<<<TEXT
        <p>
        Вы были успешно зарегистрированы. 
        <br>
        Доступ к системе будет возможен только после получения подтверждения администратором.
        </p>
TEXT
      )
    );
  }
}
