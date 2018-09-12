<?php

/**
 * Description of AgreementDealerHistoryMailSender
 *
 * @author Сергей
 */
class AgreementSpecialistHistoryMailSender
{
    static function send($mail_class, LogEntry $entry, User $specialist, $text)
    {
        if($specialist->getAllowReceiveMails()) {
            $message = new $mail_class($entry, $specialist, $text);
            $message->setPriority(1);
            sfContext::getInstance()->getMailer()->send($message);
        }
    }
}
