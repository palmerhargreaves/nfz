<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SetDefaultNotificationToFalse extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('user', 'registration_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('user', 'agreement_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('user', 'new_agreement_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('user', 'final_agreement_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('user', 'dealer_discussion_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->changeColumn('user', 'model_discussion_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
    }

    public function down()
    {

    }
}