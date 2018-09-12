<?php

/**
 * Description of UserDeactivationMail
 *
 * @author Сергей
 */
class UserDeactivationMail extends TemplatedMail
{
    function __construct(User $user)
    {
        $site_url = sfConfig::get('app_site_url');

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Аккаунт не подтверждён',
                'text' =>
<<<TEXT
        <p>
        Ваш аккаунт "{$user->getEmail()}" не был подтверждён администратором.
        <br>
        Регистрация отменена.
        </p>
TEXT
            )
        );
    }
}
