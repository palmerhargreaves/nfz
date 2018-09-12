<?php

/**
 * Description of AgreementCompleteModelMail
 *
 * @author Сергей
 */
class AgreementCompleteReportMail extends TemplatedMail
{
    function __construct(array $emails, AgreementModelReport $report)
    {
        $model = $report->getModel();

        parent::__construct(
            $emails[0],
            'agreement_activity_model_report/mail_complete_report',
            array(
                'model' => $model,
                'report' => $report,
                'dealer' => $model->getDealer(),
                'activity' => $model->getActivity()
            )
        );

        for ($n = 1, $l = count($emails); $n < $l; $n++)
            $this->addCc($emails[$n]);

        $this->attach(Swift_Attachment::fromPath($report->getFinancialDocsFileNameHelper()->getPath()));

        if ($model->getModelType()->hasAdditionalFile())
            $this->attach(Swift_Attachment::fromPath($report->getAdditionalFileNameHelper()->getPath()));
    }
}
