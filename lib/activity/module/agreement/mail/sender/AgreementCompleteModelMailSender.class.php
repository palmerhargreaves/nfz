<?php

/**
 * Description of AgreementCompleteModelMailSende
 *
 * @author Сергей
 */
class AgreementCompleteModelMailSender
{
    static function send(AgreementModel $model, $can_send_mail = true)
    {
//    $send_to_managers = !$model->isConcept() && $model->getActivity()->getName() != 'Первичная идентификация дилера';

        AgreementCompleteMailSenderUtils::sendByDealer(
            function ($emails) use ($model) {
                return new AgreementCompleteModelMail($emails, $model);
            },
            $model->getDealer(),
            $model->isConcept() ? 'final_agreement_concept_notification' : 'final_agreement_notification',
            true,
            false,//$send_to_managers
            $model->getId(),
            $can_send_mail
        );
    }
}
