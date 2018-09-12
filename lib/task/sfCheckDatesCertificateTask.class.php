<?php

class sfCheckDatesCertificateTask extends sfBaseTask
{
    const MAIL_DAYS = 7;

    protected function configure()
    {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'console'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            // add your own options here
        ));

        $this->namespace = 'sp';
        $this->name = 'sfCheckDatesCertificate';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [sfCheckDatesCertificate|INFO] task does things.
Call it with:

  [php symfony sfCheckDatesCertificate|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $today = strtotime(date('Y-m-d'));
        $activities = ActivityTable::getInstance()->createQuery()->where('allow_certificate = ?', true)->execute();

        $dealers = array();
        $total = 0;
        foreach ($activities as $activity) {
            $itemsActiveCert = AgreementModelSettingsTable::getInstance()->createQuery('s')
                ->select('s.*')
                ->leftJoin('s.Model m')
                ->leftJoin('m.Activity a')
                ->where('a.id = ?', $activity->getId())
                ->orderBy('s.certificate_date_to ASC')
                //->groupBy('m.dealer_id')
                ->execute();
            foreach ($itemsActiveCert as $itemActiveCert) {
                if (in_array($itemActiveCert->getModel()->getDealerId(), $dealers))
                    continue;

                $dealers[] = $itemActiveCert->getModel()->getDealerId();
                if (!$itemActiveCert->getMsgSend()) {
                    $certDate = strtotime(date('Y-m-d', strtotime('-' . self::MAIL_DAYS . ' days', strtotime($itemActiveCert->getCertificateDateTo()))));
                    if ($today >= $certDate) {
                        $total++;

                        $itemActiveCert->setMsgSend(true);
                        $itemActiveCert->save();

                        $mail = new ModelCertificateDate7DaysMail($itemActiveCert->getModel()->getDealer());
                        $mail->setPriority(1);

                        sfContext::getInstance()->getMailer()->send($mail);
                    }
                } else if (!$itemActiveCert->getActivateMsgSend()) {
                    $certDate = strtotime($itemActiveCert->getCertificateDateTo());
                    if ($today >= $certDate) {
                        $total++;

                        $itemActiveCert->setActivateMsgSend(true);
                        $itemActiveCert->save();

                        $modelUserSett = new AgreementModelUserSettings();
                        $modelUserSett->setArray(array('model_id' => $itemActiveCert->getModelId(),
                            'dealer_id' => $itemActiveCert->getModel()->getDealerId(),
                            'activity_id' => $activity->getId(),
                            'certificate_end' => date('Y-m-d', strtotime('+31 days', strtotime(date('Y-m-d'))))));
                        $modelUserSett->save();

                        $mail = new ModelCertificateDateActivateMail($itemActiveCert->getModel()->getDealer());
                        $mail->setPriority(1);

                        sfContext::getInstance()->getMailer()->send($mail);
                    }
                }
            }
        }
        // add your code here

        echo $total . "\r\n";
    }
}
