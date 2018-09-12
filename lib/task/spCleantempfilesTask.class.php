<?php

class spCleantempfilesTask extends sfBaseTask
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
            // add your own options here
        ));

        $this->namespace = 'sp';
        $this->name = 'clean-temp-files';
        $this->briefDescription = 'cleans old temp files';
        $this->detailedDescription = <<<EOF
The [sp:clean-temp-files|INFO] task cleans old temp files.
Call it with:

  [php symfony sp:clean-temp-files|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        // add your code here
        $files = TempFileTable::getInstance()
            ->createQuery()
            ->where(
                'date_sub(?, interval ? hour) > created_at',
                array(D::toDb(time(), true), sfConfig::get('app_saving_temp_files'))
            )
            ->execute();

        foreach ($files as $file)
            $file->delete();
    }
}
