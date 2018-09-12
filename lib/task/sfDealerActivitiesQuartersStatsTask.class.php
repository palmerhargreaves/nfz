<?php

class sfDealerActivitiesQuartersStatsTask extends sfBaseTask
{
    protected function configure()
    {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            // add your own options here
        ));

        $this->namespace = 'sp';
        $this->name = 'sfDealerActivitiesQuartersStats';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [sfDealerActivitiesQuartersStats|INFO] task does things.
Call it with:

  [php symfony sfDealerActivitiesQuartersStats|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        set_time_limit(0);
        ini_set('memory_limit', '4000M');

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $lastYear = ActivityAcceptStatsUpdatesTable::getInstance()->createQuery()->select()->fetchOne();
        if (!$lastYear) {
            $year = date('Y');
        } else {
            $year = $lastYear->getYear();
        }

        $builder = new AgreementActivityStatusStatisticBuilder($year);
        $builder->start(true);
    }
}
