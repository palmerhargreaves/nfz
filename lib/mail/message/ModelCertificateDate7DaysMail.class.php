<?php

/**
 */
class ModelCertificateDate7DaysMail extends TemplatedMail
{
    function __construct(Dealer $user)
    {
        $message = "Напоминаем Вам о необходимости предоставления информации по результатам мероприятия Service Clinic во вкладке «Статистика».";

        parent::__construct(
            $user->getEmail(),
            //'kostig51@gmail.com',
            //'emonakova@palmerhargreaves.com',
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Сервисные акции',
                'text' =>
<<<TEXT
                            {$message}
TEXT
            )
        );
    }
}
