<?php

/**
 * Description of AgreementManagementHistoryMailSender
 *
 * @author Сергей
 */
class AgreementManagementHistoryMailSender
{
    const OTHER_NOTIFICATION = 0;
    // model notifcation
    const AGREEMENT_NOTIFICATION = 1;
    const FINAL_AGREEMENT_NOTIFICATION = 2;
    const NEW_AGREEMENT_NOTIFICATION = 3;
    // report notfication
    const AGREEMENT_REPORT_NOTIFICATION = 4;
    const FINAL_AGREEMENT_REPORT_NOTIFICATION = 5;
    const NEW_AGREEMENT_REPORT_NOTIFICATION = 6;
    // concept notifcation
    const AGREEMENT_CONCEPT_NOTIFICATION = 7;
    const FINAL_AGREEMENT_CONCEPT_NOTIFICATION = 8;
    const NEW_AGREEMENT_CONCEPT_NOTIFICATION = 9;
    // concept report notfication
    const AGREEMENT_CONCEPT_REPORT_NOTIFICATION = 10;
    const FINAL_AGREEMENT_CONCEPT_REPORT_NOTIFICATION = 11;
    const NEW_AGREEMENT_CONCEPT_REPORT_NOTIFICATION = 12;

    static function send($mail_class, LogEntry $entry, $params = false, $roles = false, $type = self::OTHER_NOTIFICATION)
    {
        if (!$roles)
            $roles = array('manager');

        if (!is_array($roles))
            $roles = array($roles);

        /*$users_query = UserTable::getInstance()
                       ->createQuery('u')
                       ->innerJoin('u.Group g')
                       ->innerJoin('g.Roles r')
                       ->whereIn('r.role', $roles)
                       ->andWhere('u.active=?', true); */

        $users_query = UserTable::getInstance()
            ->createQuery('u')
            ->innerJoin('u.Group g')
            ->innerJoin('g.Roles r')
            ->whereIn('r.role', $roles)
            ->andWhere('u.active=?', true)
            ->andWhere('u.allow_receive_mails = ?', true);

        if ($type == self::AGREEMENT_NOTIFICATION)
            $users_query->andWhere('u.agreement_notification=?', true);
        if ($type == self::FINAL_AGREEMENT_NOTIFICATION)
            $users_query->andWhere('u.final_agreement_notification=?', true);
        if ($type == self::NEW_AGREEMENT_NOTIFICATION)
            $users_query->andWhere('u.new_agreement_notification=?', true);

        if ($type == self::AGREEMENT_REPORT_NOTIFICATION)
            $users_query->andWhere('u.agreement_report_notification=?', true);
        if ($type == self::FINAL_AGREEMENT_REPORT_NOTIFICATION)
            $users_query->andWhere('u.final_agreement_report_notification=?', true);
        if ($type == self::NEW_AGREEMENT_REPORT_NOTIFICATION)
            $users_query->andWhere('u.new_agreement_report_notification=?', true);

        if ($type == self::AGREEMENT_CONCEPT_NOTIFICATION)
            $users_query->andWhere('u.agreement_concept_notification=?', true);
        if ($type == self::FINAL_AGREEMENT_CONCEPT_NOTIFICATION)
            $users_query->andWhere('u.final_agreement_concept_notification=?', true);
        if ($type == self::NEW_AGREEMENT_CONCEPT_NOTIFICATION)
            $users_query->andWhere('u.new_agreement_concept_notification=?', true);

        if ($type == self::AGREEMENT_CONCEPT_REPORT_NOTIFICATION)
            $users_query->andWhere('u.agreement_concept_report_notification=?', true);
        if ($type == self::FINAL_AGREEMENT_CONCEPT_REPORT_NOTIFICATION)
            $users_query->andWhere('u.final_agreement_concept_report_notification=?', true);
        if ($type == self::NEW_AGREEMENT_CONCEPT_REPORT_NOTIFICATION)
            $users_query->andWhere('u.new_agreement_concept_report_notification=?', true);

        $model = AgreementModelTable::getInstance()->find($entry->getObjectId());
        foreach ($users_query->execute() as $user) {
            /*$dealersList = $user->getDealersList();

            if(!empty($dealersList) && $model)
            {
              if(in_array($model->getDealer()->getId(), $dealersList)) {
                self::sendMail($mail_class, $entry, $user, $params);
              }
            }
            else if($user->isManager())
              self::sendMail($mail_class, $entry, $user, $params);
            */
            if($user->getAllowReceiveMails()) {
                self::sendMail($mail_class, $entry, $user, $params);
            }
        }

    }

    private static function sendMail($mail_class, $entry, $user, $params)
    {
        $message = $params ? new $mail_class($entry, $user, $params) : new $mail_class($entry, $user);
        $message->setParams($params);
        $message->setPriority(1);
        sfContext::getInstance()->getMailer()->send($message);
    }
}
