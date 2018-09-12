<?php

/**
 * Description of DealerUserRegisteredForUserMail
 *
 * @author Сергей
 */
class DealerUserRegisteredForUserMail extends TemplatedMail
{
  function __construct(User $user, $password = '')
  {
      $site_url = sfConfig::get('app_site_url');

    parent::__construct(
      $user->getEmail(),
      //'emonakova@palmerhargreaves.com',
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
          <br>
          После подтверждения Вы можете использовать Ваш логин "{$user->getEmail()}" и пароль "{$password}" для <a href="{$site_url}">входа в систему</a>
        </p>

TEXT
      )
    );
  }
}
