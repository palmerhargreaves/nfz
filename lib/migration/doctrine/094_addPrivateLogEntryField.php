<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddPrivateLogEntryField extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('log', 'private_user_id', 'integer', '8', array(
             'notnull' => '',
             'default' => '0',
             ));
        $this->createForeignKey('log', 'log_private_user_id_user_id', array(
             'name' => 'log_private_user_id_user_id',
             'local' => 'private_user_id',
             'foreign' => 'id',
             'foreignTable' => 'user',
             ));
        $this->addIndex('log', 'log_private_user_id', array(
             'fields' => 
             array(
              0 => 'private_user_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('log', 'log_private_user_id_user_id');
        $this->removeIndex('log', 'log_private_user_id', array(
             'fields' => 
             array(
              0 => 'private_user_id',
             ),
             ));
        $this->removeColumn('log', 'private_user_id');
    }
}