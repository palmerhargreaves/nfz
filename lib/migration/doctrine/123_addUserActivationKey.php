<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddUserActivationKey extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('user', 'activation_key', 'string', '255', array(
             'notnull' => '',
             ));
    }

    public function down()
    {
        $this->removeColumn('user', 'activation_key');
    }
}