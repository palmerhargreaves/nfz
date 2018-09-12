<?php

/**
 */
class ActivityDealersSendMail extends TemplatedMail
{
    function __construct(User $user, $msg)
    {
            parent::__construct(
                $user->getEmail(),
                //'kostig51@gmail.com',
                //'emonakova@palmerhargreaves.com',
                'global/mail_common_dealers',
                array(
                    'user' => $user,
                    'subject' => 'Сервисные акции',
                    'text' =>
<<<TEXT
{$msg}
TEXT
                )
            );
        }

}
