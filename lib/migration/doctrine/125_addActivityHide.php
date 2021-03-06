<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddActivityHide extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('activity', 'hide', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->addIndex('activity', 'hidden_sort', array(
             'fields' => 
             array(
              0 => 'hide',
              1 => 'finished',
              2 => 'importance',
              3 => 'sort',
              4 => 'id',
             ),
             ));
    }

    public function down()
    {
        $this->removeIndex('activity', 'hidden_sort', array(
             'fields' => 
             array(
              0 => 'hide',
              1 => 'finished',
              2 => 'importance',
              3 => 'sort',
              4 => 'id',
             ),
             ));
        $this->removeColumn('activity', 'hide');
    }
}