<?php

class spAgreementupdateactivitystatusTask extends sfBaseTask
{
    /**
     * The agreement module
     *
     * @var ActivityModule
     */
    protected $agreement_module = null;

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
        $this->name = 'agreement-update-activity-status';
        $this->briefDescription = 'updates activity statuses by the agreement module';
        $this->detailedDescription = <<<EOF
The [sp:agreement-update-activity-status|INFO] task updates activity statuses by the agreement module.
Call it with:

  [php symfony sp:agreement-update-activity-status|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        // add your code here

        // Этот запрос приводит к ошибке сортировки, потому придётся использовать обходной вариант.
        // Если нужно, можно потом для макетов отменить сортировку по умолчанию и установить её
        // в ручную в тех местах, где это нужно.
//    $updates = UpdateActivityStatusTable::getInstance()
//               ->createQuery('u')
//               ->leftJoin('u.Activity a')
//               ->leftJoin('a.Modules m WITH m.identifier=?', 'agreement')
//               ->execute();
        $updates = UpdateActivityStatusTable::getInstance()
            ->createQuery('u')
            ->leftJoin('u.Activity a')
            ->execute();

        $found = $updates->count();

        echo 'Found updates: ', $found, "\r\n";

        $processed = 0;
        foreach ($updates as $update) {
            $activity = $update->getActivity();
            $dealer = $update->getDealer();

            // отдельная проверка на подключение модуля и есть обходной путь
            if ($activity && $dealer && $this->hasAgreementModule($activity)) {
                $utils = new AgreementActivityStatusUtils($activity, $dealer);
                $utils->updateActivityAcceptance();
                $processed++;
            }

            $update->delete();
        }

        echo 'Processed: ', $processed, "\r\n";
    }

    protected function hasAgreementModule(Activity $activity)
    {
        if (!$this->getAgreementModule())
            return false;

        return AcivityModuleActivityTable::getInstance()
            ->createQuery()
            ->where('activity_id=? and module_id=?', array($activity->getId(), $this->getAgreementModule()->getId()))
            ->count() > 0;
    }

    /**
     * Returns the agreement module
     *
     * @return ActivityModule|null
     */
    protected function getAgreementModule()
    {
        if ($this->agreement_module === null) {
            $this->agreement_module = ActivityModuleTable::getInstance()->findOneByIdentifier('agreement');
        }
        return $this->agreement_module ?: null;
    }
}
