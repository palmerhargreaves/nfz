<?php

/**
 * Description of DealerUserRegisteredForImporterMail
 *
 * @author Сергей
 */
class UserActivationMail extends TemplatedMail
{
  function __construct(User $user, $password = '')
  {
    parent::__construct(
        $user->getEmail(),
        'global/mail_common_register',
        array(
            'user' => $user,
            'subject' => 'Активация',
            'text' =>
<<<TEXT
TEXT
          )
      );
  }
}
