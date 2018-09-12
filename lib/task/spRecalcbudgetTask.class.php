<?php

class spRecalcbudgetTask extends sfBaseTask
{
    protected function configure()
    {
        // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('year', sfCommandArgument::REQUIRED, 'an year'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'console'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            // add your own options here
        ));

        $this->namespace = 'sp';
        $this->name = 'recalc-budget';
        $this->briefDescription = 'recalculates real budget';
        $this->detailedDescription = <<<EOF
The [sp:recalc-budget|INFO] task recalculates real budget.
Call it with:

  [php symfony sp:recalc-budget|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        // add your code here
        $count = DealerTable::getVwDealersQuery()->count();
        $n = $prev = 0;
        foreach (DealerTable::getVwDealersQuery()->execute() as $dealer) {
            RealTotalBudgetTable::getInstance()->recalculate($dealer, $arguments['year']);

            $percent = round($n / ($count - 1) * 100);
            if ($percent % 10 == 0 && $percent != $prev) {
                $prev = $percent;
                echo $percent, "%\r\n";
            }

            $n++;
        }

        echo "done\r\n";
    }
}
