<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddTimestampForDealerUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('dealer_user', 'created_at', 'timestamp', '25', array(
             'notnull' => '1',
             ));
        $this->addColumn('dealer_user', 'updated_at', 'timestamp', '25', array(
             'notnull' => '1',
             ));
    }

    public function down()
    {
        $this->removeColumn('dealer_user', 'created_at');
        $this->removeColumn('dealer_user', 'updated_at');
    }
}