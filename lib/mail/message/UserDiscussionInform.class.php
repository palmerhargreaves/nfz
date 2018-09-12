<?php

/**
 * UserDiscussionInform
 *
 *
 */
class UserDiscussionInform extends TemplatedMail
{
    function __construct(User $user, Message $message)
    {
        $site_url = sfConfig::get('app_site_url');

        parent::__construct(
            $user->getEmail(),
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Новое сообщение',
                'text' =>
<<<TEXT
                            <p>
        {$message->getText()}
        <br>
        С уважением,
        Команда Servicepool
        </p>
TEXT
            )
        );
    }
}
