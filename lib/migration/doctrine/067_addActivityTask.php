<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddActivityTask extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('activity_task', array(
             'id' => 
             array(
              'type' => 'integer',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '8',
             ),
             'name' => 
             array(
              'type' => 'string',
              'notnull' => '1',
              'length' => '255',
             ),
             'activity_id' => 
             array(
              'type' => 'integer',
              'notnull' => '1',
              'length' => '8',
             ),
             'created_at' => 
             array(
              'notnull' => '1',
              'type' => 'timestamp',
              'length' => '25',
             ),
             'updated_at' => 
             array(
              'notnull' => '1',
              'type' => 'timestamp',
              'length' => '25',
             ),
             ), array(
             'type' => 'MyISAM',
             'primary' => 
             array(
              0 => 'id',
             ),
             'collate' => 'utf8_unicode_ci',
             'charset' => 'utf8',
             ));
        $this->createForeignKey('activity_task', 'activity_task_activity_id_activity_id', array(
             'name' => 'activity_task_activity_id_activity_id',
             'local' => 'activity_id',
             'foreign' => 'id',
             'foreignTable' => 'activity',
             ));
        $this->addIndex('activity_task', 'activity_task_activity_id', array(
             'fields' => 
             array(
              0 => 'activity_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('activity_task', 'activity_task_activity_id_activity_id');
        $this->removeIndex('activity_task', 'activity_task_activity_id', array(
             'fields' => 
             array(
              0 => 'activity_id',
             ),
             ));
        $this->dropTable('activity_task');
    }
}