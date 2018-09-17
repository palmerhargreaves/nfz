<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ActivityExtendedStatisticSteps', 'doctrine');

/**
 * BaseActivityExtendedStatisticSteps
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $action_after
 * @property integer $activity_id
 * @property string $header
 * @property string $description
 * @property enum $stat_type
 * @property integer $position
 * 
 * @method integer                        getId()             Returns the current record's "id" value
 * @method integer                        getActionAfter()             Returns the current record's "action_after" value
 * @method integer                        getActivityId()             Returns the current record's "activity_id" value
 * @method string                         getHeader()             Returns the current record's "header" value
 * @method string                         getDescription()             Returns the current record's "description" value
 * @method enum                           getStepType()             Returns the current record's "stat_type" value
 * @method integer                        getPosition()             Returns the current record's "position" value
 * @method ActivityExtendedStatisticSteps setId()             Sets the current record's "id" value
 * @method ActivityExtendedStatisticSteps setActionAfter()             Sets the current record's "action_after" value
 * @method ActivityExtendedStatisticSteps setActivityId()             Sets the current record's "activity_id" value
 * @method ActivityExtendedStatisticSteps setHeader()             Sets the current record's "header" value
 * @method ActivityExtendedStatisticSteps setDescription()             Sets the current record's "description" value
 * @method ActivityExtendedStatisticSteps setStepType()             Sets the current record's "stat_type" value
 * @method ActivityExtendedStatisticSteps setPosition()             Sets the current record's "position" value
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseActivityExtendedStatisticSteps extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('activity_extended_statistic_steps');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('action_after', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'notnull' => true,
             ));
        $this->hasColumn('activity_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'notnull' => true,
             ));
        $this->hasColumn('header', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'notnull' => true,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'notnull' => true,
             ));
        $this->hasColumn('step_type', 'enum', 8, array(
             'type' => 'enum',
             'fixed' => 0,
             'unsigned' => false,
             'values' => 
             array(
              0 => 'none',
              1 => 'mail_action_end',
              2 => 'mail_certificate_end',
             ),
             'primary' => false,
             'notnull' => true,
             'length' => 8,
             ));
        $this->hasColumn('position', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'notnull' => true,
             ));


        $this->index('activity_id', array(
             'fields' => 
             array(
              0 => 'activity_id',
             ),
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