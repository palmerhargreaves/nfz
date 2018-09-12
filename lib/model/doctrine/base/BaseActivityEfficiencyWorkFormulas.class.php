<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ActivityEfficiencyWorkFormulas', 'doctrine');

/**
 * BaseActivityEfficiencyWorkFormulas
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property interger $position
 * 
 * @method integer                        getId()       Returns the current record's "id" value
 * @method string                         getName()     Returns the current record's "name" value
 * @method string                         getType()     Returns the current record's "type" value
 * @method interger                       getPosition() Returns the current record's "position" value
 * @method ActivityEfficiencyWorkFormulas setId()       Sets the current record's "id" value
 * @method ActivityEfficiencyWorkFormulas setName()     Sets the current record's "name" value
 * @method ActivityEfficiencyWorkFormulas setType()     Sets the current record's "type" value
 * @method ActivityEfficiencyWorkFormulas setPosition() Sets the current record's "position" value
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseActivityEfficiencyWorkFormulas extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('activity_efficiency_work_formulas');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'notnull' => false,
             ));
        $this->hasColumn('type', 'string', 80, array(
             'type' => 'string',
             'length' => 80,
             'notnull' => false,
             ));
        $this->hasColumn('position', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'notnull' => false,
             'default' => 0,
             ));

        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();

        
    }
}