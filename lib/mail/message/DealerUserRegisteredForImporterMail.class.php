<?php

/**
 * Description of DealerUserRegisteredForImporterMail
 *
 * @author Сергей
 */
class DealerUserRegisteredForImporterMail extends TemplatedMail
{
    function __construct(User $user, User $importer)
    {
        $activation_url = sfContext::getInstance()->getController()->genUrl(
            '@activate_dealer_user?id=' . $user->getId() . '&key=' . $user->getActivationKey(),
            true
        );
        $deactivation_url = sfContext::getInstance()->getController()->genUrl(
            '@deactivate_dealer_user?id=' . $user->getId() . '&key=' . $user->getActivationKey(),
            true
        );

        $dealer = $user->getDealerUsers()->getFirst()->getDealer();

        parent::__construct(
            //'emonakova@palmerhargreaves.com',
            //$importer->getEmail(),
            sfConfig::get('app_mail_sender'),
            'global/mail_common',
            array(
                'user' => $importer,
                'subject' => 'Регистрация',
                'text' =>
<<<TEXT
                            <p>
        В системе был зарегистрирован новый пользователь с e-mail "{$user->getEmail()}".
        <br>
        Он указал, что относится к дилерскому предприятию "{$dealer->getName()}" ({$dealer->getNumber()}).
        </p>
        <p>
        <strong>Используйте <a href="{$activation_url}">эту ссылку</a> для активации пользователя.</strong>
        </p>
        <p>
        <small>Используйте <a href="{$deactivation_url}">эту ссылку</a> для отмены регистрации пользователя.</small>
        </p>
TEXT
            )
        );
    }
}
