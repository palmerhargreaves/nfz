<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ActivityTask', 'doctrine');

/**
 * BaseActivityTask
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property integer $activity_id
 * @property Activity $Activity
 * @property Doctrine_Collection $Results
 * 
 * @method integer             getId()          Returns the current record's "id" value
 * @method string              getName()        Returns the current record's "name" value
 * @method integer             getActivityId()  Returns the current record's "activity_id" value
 * @method Activity            getActivity()    Returns the current record's "Activity" value
 * @method Doctrine_Collection getResults()     Returns the current record's "Results" collection
 * @method ActivityTask        setId()          Sets the current record's "id" value
 * @method ActivityTask        setName()        Sets the current record's "name" value
 * @method ActivityTask        setActivityId()  Sets the current record's "activity_id" value
 * @method ActivityTask        setActivity()    Sets the current record's "Activity" value
 * @method ActivityTask        setResults()     Sets the current record's "Results" collection
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseActivityTask extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('activity_task');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('activity_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('is_concept_complete', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             'notnull' => true,
             ));
        $this->hasColumn('position', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));

        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Activity', array(
             'local' => 'activity_id',
             'foreign' => 'id'));

        $this->hasMany('ActivityTaskResult as Results', array(
             'local' => 'id',
             'foreign' => 'task_id',
             'cascade' => array(
             0 => 'delete',
             )));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}