<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddUpdateActivityStatus extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('update_activity_status', array(
             'id' => 
             array(
              'type' => 'integer',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '8',
             ),
             'activity_id' => 
             array(
              'type' => 'integer',
              'notnull' => '1',
              'length' => '8',
             ),
             'dealer_id' => 
             array(
              'type' => 'integer',
              'notnull' => '1',
              'length' => '8',
             ),
             ), array(
             'type' => 'MyISAM',
             'indexes' => 
             array(
              'relation' => 
              array(
              'fields' => 
              array(
               0 => 'activity_id',
               1 => 'dealer_id',
              ),
              ),
             ),
             'primary' => 
             array(
              0 => 'id',
             ),
             'collate' => 'utf8_unicode_ci',
             'charset' => 'utf8',
             ));
        $this->createForeignKey('update_activity_status', 'update_activity_status_activity_id_activity_id', array(
             'name' => 'update_activity_status_activity_id_activity_id',
             'local' => 'activity_id',
             'foreign' => 'id',
             'foreignTable' => 'activity',
             ));
        $this->createForeignKey('update_activity_status', 'update_activity_status_dealer_id_dealers_id', array(
             'name' => 'update_activity_status_dealer_id_dealers_id',
             'local' => 'dealer_id',
             'foreign' => 'id',
             'foreignTable' => 'dealers',
             ));
        $this->addIndex('update_activity_status', 'update_activity_status_activity_id', array(
             'fields' => 
             array(
              0 => 'activity_id',
             ),
             ));
        $this->addIndex('update_activity_status', 'update_activity_status_dealer_id', array(
             'fields' => 
             array(
              0 => 'dealer_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('update_activity_status', 'update_activity_status_activity_id_activity_id');
        $this->dropForeignKey('update_activity_status', 'update_activity_status_dealer_id_dealers_id');
        $this->removeIndex('update_activity_status', 'update_activity_status_activity_id', array(
             'fields' => 
             array(
              0 => 'activity_id',
             ),
             ));
        $this->removeIndex('update_activity_status', 'update_activity_status_dealer_id', array(
             'fields' => 
             array(
              0 => 'dealer_id',
             ),
             ));
        $this->dropTable('update_activity_status');
    }
}