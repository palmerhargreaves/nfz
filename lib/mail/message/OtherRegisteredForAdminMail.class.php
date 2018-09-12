<?php

/**
 * Description of DealerUserRegisteredForImporterMail
 *
 * @author Сергей
 */
class OtherRegisteredForAdminMail extends TemplatedMail
{
  function __construct(User $user, User $admin)
  {
    parent::__construct(
        //'emonakova@palmerhargreaves.com',
      //$admin->getEmail(),
        sfConfig::get('app_mail_sender'),
      'global/mail_common',
      array(
        'user' => $admin,
        'subject' => 'Регистрация',
        'text' =>
<<<TEXT
        <p>
        В системе был зарегистрирован новый пользователь: "{$user->getEmail()}",
        компания "{$user->getCompanyName()}".
        </p>
TEXT
      )
    );
  }
}
