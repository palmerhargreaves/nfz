<?php

/**
 * Description of AgreementDealerHistoryMailSender
 *
 * @author Сергей
 */
class AgreementDealerHistoryMailSender
{
    static function send($mail_class, LogEntry $entry, Dealer $dealer, Message $mail_message = null, $can_send_mail = true)
    {
        $users = UserTable::getInstance()
            ->createQuery('u')
            ->innerJoin('u.DealerUsers du WITH du.dealer_id=?', $dealer->getId())
            ->where('u.active=?', true)
            ->execute();

        foreach ($users as $user) {
            if($user->getAllowReceiveMails()) {
                if($mail_class == "AgreementModelDeclinedMail") {
                    $message = new $mail_class($entry, $user, $mail_message);
                } else {
                    $message = new $mail_class($entry, $user);
                }

                $message->setPriority(1);
                if (!$can_send_mail) {
                    $message->setCanSendMail($can_send_mail);
                    $message->setModelId($entry->getObjectId());
                }

                sfContext::getInstance()->getMailer()->send($message);
            }
        }
    }
}
