<?php

/**
 * Description of AgreementCompleteMailSenderUtils
 *
 * @author Сергей
 */
class AgreementCompleteMailSenderUtils
{
    static function sendByDealer(Closure $mail_creator, Dealer $dealer, $agreement_field, $send_to_importers = true, $send_to_managers = true, $model_id = 0, $can_send_mail = true)
    {
        $emails = array_values(array_filter(self::getCompleteMailRecepients($dealer, $agreement_field, $send_to_importers, $send_to_managers)));

        if ($emails) {
            $message = $mail_creator($emails);
            $message->setPriority(1);

            if (!$can_send_mail) {
                $message->setCanSendMail($can_send_mail);
                $message->setModelId($model_id);
            }

            sfContext::getInstance()->getMailer()->send($message);
        }
    }

    private static function getCompleteMailRecepients(Dealer $dealer, $agreement_field, $with_importers, $with_managers)
    {
        return array_values(array_unique(array_merge(
            self::getDealerUserEmails($dealer),
            self::getManagerEmails($agreement_field),
            self::getRegionalManagersEmails($dealer),
            $with_importers ? self::getImporterEmails($agreement_field) : array(),
            $with_managers ? self::getRegionalManagerEmails($dealer) : array()
        )));
    }

    private static function getDealerUserEmails(Dealer $dealer)
    {
        $emails = array();

        foreach ($dealer->getDealerUsers() as $dealer_user) {
            $user = $dealer_user->getUser();
            if ($user && $user->getActive() && $user->getAllowReceiveMails()) {
                $emails[] = $user->getEmail();
            }
        }

        return $emails;
    }

    private static function getRegionalManagersEmails(Dealer $dealer) {
        $emails = array();

        if ($dealer->getNfzRegionalManagerId() != 0) {
            $person = NaturalPersonTable::getInstance()->createQuery()->where('id = ?', $dealer->getNfzRegionalManagerId())->fetchOne();
            if ($person && $person->getRegionalManagerNfzId() != 0) {
                $user = UserTable::getInstance()->find($person->getRegionalManagerNfzId());

                if ($user && $user->getActive() && $user->getAllowToGetDealersMessages()) {
                    $emails[] = $user->getEmail();
                }
            }
        }

        return $emails;
    }

    private static function getImporterEmails($agreement_field)
    {
        $emails = array();

        $importers = UserTable::getInstance()
            ->createQuery('u')
            ->innerJoin('u.Group g')
            ->innerJoin('g.Roles r')
            ->andWhere('r.role=?', 'importer')
            ->andWhere('u.allow_receive_mails = ?', true)
            ->andWhere("u.active=? and u.$agreement_field=?", array(true, true))
            ->execute();

        foreach ($importers as $importer)
            $emails[] = $importer->getEmail();

        return $emails;
    }

    private static function getRegionalManagerEmails(Dealer $dealer)
    {
        $emails = array();

        $manager = $dealer->getRegionalManager();

        if (!$manager)
            return $emails;

        foreach ($manager->getUsers() as $user)
            $emails[] = $user->getEmail();

        return $emails;
    }

    private static function getManagerEmails($agreement_field)
    {
        $users_query = UserTable::getInstance()
            ->createQuery('u')
            ->innerJoin('u.Group g')
            ->innerJoin('g.Roles r')
            ->where('r.role=?', 'manager')
            ->andWhere('u.allow_receive_mails = ?', true)
            ->andWhere("u.active=? and u.$agreement_field=?", array(true, true));

        $emails = array();
        foreach ($users_query->execute() as $user)
            $emails[] = $user->getEmail();

        return $emails;
    }
}
