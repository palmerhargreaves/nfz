<?php

class spAgreementrecalcrealbudgetTask extends sfBaseTask
{
    protected $agreement_module = null;

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('year', sfCommandArgument::REQUIRED, 'an year'),
            new sfCommandArgument('limit', sfCommandArgument::OPTIONAL, 'limit'),
            new sfCommandArgument('offset', sfCommandArgument::OPTIONAL, 'offset'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'console'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            // add your own options here
        ));

        $this->namespace = 'sp';
        $this->name = 'agreement-recalc-real-budget';
        $this->briefDescription = 'recalculates real budget by reports';
        $this->detailedDescription = <<<EOF
The [sp:agreement-recalc-real-budget|INFO] task recalculates real budget by reports.
Call it with:

  [php symfony sp:agreement-recalc-real-budget|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        // add your code here
        $year = $arguments['year'];
        $limit = $arguments['limit'];
        $offset = $arguments['offset'];

        if ($offset == 0) {
            echo "Removing old budget...\r\n";
            $removed = $this->removeOldBudget($year);
            echo 'Removed: ', $removed, "\r\n";
        }


        echo "Calculating budget...\r\n";
        $this->calcRealBudget($year, $limit, $offset);
        echo "Done!\r\n";
        echo "Execute 'sp:recalc-budget $year' to calculate total budget\r\n";
    }

    protected function removeOldBudget($year)
    {
        // здесь пропускаем бюджеты, установленные вручную (object_id==0)
        return RealBudgetTable::getInstance()
            ->createQuery()
            ->delete()
            ->where(
                'year=? and module_id=? and object_id>?',
                array($year, $this->getAgreementModule()->getId(), 0)
            )
            ->execute();
    }

    protected function calcRealBudget($year, $limit = null, $offset = null)
    {
        $dealers = DealerTable::getVwDealersQuery($limit, $offset)->execute(); //->offset
        $count = $dealers->count();
        $n = $prev = 0;

        if (!(empty($dealers)))
            foreach ($dealers as $dealer) {
                $this->calculateBudgetByDealer($dealer, $year);

                $percent = round($n / ($count - 1) * 100);
                if ($percent % 10 == 0 && $percent != $prev) {
                    $prev = $percent;
                    echo $percent, "%\r\n";
                }

                $n++;
            }
    }

    protected function calculateBudgetByDealer(Dealer $dealer, $year)
    {
        $reports = AgreementModelReportTable::getInstance()
            ->createQuery('r')
            ->innerJoin('r.Model m WITH m.dealer_id=?', $dealer->getId())
            ->innerJoin('m.ModelType t WITH t.concept=?', false)
            ->where('r.status=? and year(r.created_at)=?', array('accepted', $year))
            ->execute();

        foreach ($reports as $report) {
            $model = $report->getModel();

            RealBudgetTable::getInstance()->addByReportDate(
                $model->getDealer(),
                $model->getCost(),
                $this->getAgreementModule(),
                $report->created_at,
                $model->getId()
            );
        }
    }

    /**
     * Returns the agreement module
     *
     * @return ActivityModule
     */
    protected function getAgreementModule()
    {
        if (!$this->agreement_module) {
            $this->agreement_module = ActivityModule::byIdentifier('agreement');
        }
        return $this->agreement_module;
    }
}
