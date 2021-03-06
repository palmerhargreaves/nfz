<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Calendar', 'doctrine');

/**
 * BaseCalendar
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $title
 * @property string $start_date
 * @property string $end_date
 * 
 * @method integer  getId()         Returns the current record's "id" value
 * @method string   getTitle()      Returns the current record's "title" value
 * @method string   getStartDate()  Returns the current record's "start_date" value
 * @method string   getEndDate()    Returns the current record's "end_date" value
 * @method Calendar setId()         Sets the current record's "id" value
 * @method Calendar setTitle()      Sets the current record's "title" value
 * @method Calendar setStartDate()  Sets the current record's "start_date" value
 * @method Calendar setEndDate()    Sets the current record's "end_date" value
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCalendar extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('calendar');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('start_date', 'string', 30, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 30,
             ));
        $this->hasColumn('end_date', 'string', 30, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 30,
             ));

        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}