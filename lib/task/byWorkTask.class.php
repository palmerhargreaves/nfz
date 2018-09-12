<?php

class byWorkTask extends sfBaseTask
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
        $this->name = 'byWork';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [byWork|INFO] task does things.
Call it with:

  [php symfony byWork|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();


        $total_new_items = 0;
        $items = DealerUserTable::getInstance()->createQuery()->orderBy('id ASC')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach ($items as $item) {
            if (DealerUserPkwNfzTable::getInstance()->createQuery()->where('user_id = ? and dealer_id = ?', array($item['user_id'], $item['dealer_id']))->count() == 0) {
                $dealer_user_item = new DealerUserPkwNfz();

                unset($item['id']);
                $dealer_user_item->setArray($item);
                $dealer_user_item->save();

                $total_new_items++;
            }
        }

        echo $total_new_items;
        // add your code here
    }
}
