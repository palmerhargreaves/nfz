<?php

class spAcceptactivitiesTask extends sfBaseTask
{
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
            new sfCommandOption('run', null, sfCommandOption::PARAMETER_REQUIRED, 'Accept running', 'no'),
            // add your own options here
        ));

        $this->namespace = 'sp';
        $this->name = 'accept-activities';
        $this->briefDescription = 'accepts activities by accepted reports';
        $this->detailedDescription = <<<EOF
The [sp:accept-activities|INFO] task accepts activities by accepted reports.
Call it with:

  [php symfony sp:accept-activities|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        if ($options['run'] != 'yes') {
            echo "Use option '--run=yes' to accept running of this task\r\n";
            return;
        }

        sfContext::createInstance($this->configuration);

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        // add your code here
        $models = $this->findToAccept();

        foreach ($models as $model) {
            $report = $model->getReport();
            $report->setAcceptProcessed(true);

            if (!$report->getAcceptDate())
                $report->setAcceptDate($report->updated_at);

            $report->save();

            $this->acceptActivity($model->getActivity(), $model->getDealer());
            UpdateActivityStatusTable::getInstance()->shedule($model->getActivity(), $model->getDealer());
        }

        // echo "Activity tasks was updated.\r\n";
        // echo "Run sp:agreement-update-activity-status to update of activties status\r\n";
        echo 'Found models: ', $models->count(), "\r\n";
    }

    protected function findToAccept()
    {
        return AgreementModelTable::getInstance()
            ->createQuery('m')
            ->select('m.*, a.id, r.*')
            ->innerJoin('m.Report r WITH r.status=?', 'accepted')
            ->innerJoin('m.Activity a')
            ->where('r.accept_processed=?', false)
            ->orderBy('r.updated_at')
            ->execute();
    }

    protected function acceptActivity(Activity $activity, Dealer $dealer)
    {
        foreach ($activity->getTasks() as $task) {
            $result = ActivityTaskResultTable::getInstance()
                ->createQuery()
                ->where('task_id=? and dealer_id=?', array($task->getId(), $dealer->getId()))
                ->fetchOne();

            if (!$result) {
                $result = new ActivityTaskResult();
                $result->setTaskId($task->getId());
                $result->setDealerId($dealer->getId());
            }

            $result->setDone(true);
            $result->save();
        }
    }
}
