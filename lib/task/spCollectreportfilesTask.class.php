<?php

class spCollectreportfilesTask extends sfBaseTask
{
    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('activity_id', sfCommandArgument::REQUIRED, 'Activity id'),
        ));

        $this->addOptions(array(
            new sfCommandOption('dry', null, sfCommandOption::PARAMETER_OPTIONAL, 'If true to don\'t copy', 'false'),
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            // add your own options here
        ));

        $this->namespace = 'sp';
        $this->name = 'collect-report-files';
        $this->briefDescription = 'collects report files';
        $this->detailedDescription = <<<EOF
The [sp:collect-report-files|INFO] task collects report files.
Call it with:

  [php symfony sp:collect-report-files|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        // add your code here
        $reports = AgreementModelReportTable::getInstance()
            ->createQuery('r')
            ->innerJoin('r.Model m WITH m.activity_id=?', $arguments['activity_id'])
//              ->innerJoin('r.Model m')
            ->innerJoin('m.ModelType mt WITH mt.report_field_description=?', 'Фотоотчёт')
            ->where('r.status=? and r.additional_file<>""', 'accepted')
            ->execute();

        $collection_path = sfConfig::get('sf_web_dir') . '/collection';

        if ($options['dry'] != 'false') {
            echo "Dry mode is on!\r\n";
        } else {
            if (!file_exists($collection_path))
                mkdir($collection_path);
        }

        foreach ($reports as $report) {
            $helper = $report->getAdditionalFileNameHelper();
            $src = $helper->getPath();
            $dst = $collection_path
                . '/'
                . $report->getModel()->getDealer()->getNumber()
                . '-' . $report->getModel()->getActivityId()
                . '-' . $report->getModel()->getId()
                . '.' . $helper->getExtension();

            echo $src, ' -> ', $dst, "\r\n";

            if ($options['dry'] == 'false')
                copy($src, $dst);
        }

        if ($options['dry'] != 'false')
            echo "Dry mode is on!\r\n";
    }
}
