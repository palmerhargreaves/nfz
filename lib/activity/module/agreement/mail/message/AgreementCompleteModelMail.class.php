<?php

/**
 * Description of AgreementCompleteModelMail
 *
 * @author Сергей
 */
class AgreementCompleteModelMail extends TemplatedMail
{
    function __construct(array $emails, AgreementModel $model)
    {
        if (!empty($emails[0])) {
            parent::__construct(
                $emails[0],
                'agreement_activity_model/mail_complete_model',
                array(
                    'model' => $model,
                    'dealer' => $model->getDealer(),
                    'activity' => $model->getActivity()
                )
            );

            for ($n = 1, $l = count($emails); $n < $l; $n++)
                $this->addCc($emails[$n]);

            $this->attach(Swift_Attachment::fromPath($model->getModelFileNameHelper()->getPath()));
        }
    }
}
