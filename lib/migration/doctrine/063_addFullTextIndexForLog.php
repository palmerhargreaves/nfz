<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddFullTextIndexForLog extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addIndex('log', 'content', array(
             'fields' => 
             array(
              0 => 'title',
              1 => 'description',
             ),
             'type' => 'fulltext',
             ));
    }

    public function down()
    {
        $this->removeIndex('log', 'content', array(
             'fields' => 
             array(
              0 => 'title',
              1 => 'description',
             ),
             'type' => 'fulltext',
             ));
    }
}