<?php

class sendMailNewsTask extends sfBaseTask
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
        $this->name = 'sendMailNews';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [sendMailNews|INFO] task does things.
Call it with:

  [php symfony sendMailNews|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $data = NewsTable::getInstance()->createQuery()->where('id = ?', 7)->fetchOne();
        $dealers = DealerTable::getInstance()->createQuery()->where('status = ? and email IS NOT NULL', 1)->orderBy('id ASC')->execute();

        //foreach($dealers as $dealer) {
        $mail = new NewsMail($dealers->getFirst(), $data);
        $mail->setPriority(1);

        sfContext::getInstance()->getMailer()->send($mail);
        //}

        // add your code here
    }
}
