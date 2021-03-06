<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('DealerServicesDialogs', 'doctrine');

/**
 * BaseDealerServicesDialogs
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $year
 * @property string $header
 * @property clob $description
 * @property string $confirm_bt1_left
 * @property string $confirm_bt1_right
 * @property string $confirm_bt2_left
 * @property string $confirm_bt2_right
 * @property clob $confirm_msg
 * @property integer $width
 * @property integer $left_pos
 * @property string $template
 * @property boolean $status
 * @property date $start_date
 * @property date $end_date
 * @property integer $activity_id
 * 
 * @method integer               getId()                Returns the current record's "id" value
 * @method integer               getYear()              Returns the current record's "year" value
 * @method string                getHeader()            Returns the current record's "header" value
 * @method clob                  getDescription()       Returns the current record's "description" value
 * @method string                getConfirmBt1Left()    Returns the current record's "confirm_bt1_left" value
 * @method string                getConfirmBt1Right()   Returns the current record's "confirm_bt1_right" value
 * @method string                getConfirmBt2Left()    Returns the current record's "confirm_bt2_left" value
 * @method string                getConfirmBt2Right()   Returns the current record's "confirm_bt2_right" value
 * @method clob                  getConfirmMsg()        Returns the current record's "confirm_msg" value
 * @method integer               getWidth()             Returns the current record's "width" value
 * @method integer               getLeftPos()           Returns the current record's "left_pos" value
 * @method string                getTemplate()          Returns the current record's "template" value
 * @method boolean               getStatus()            Returns the current record's "status" value
 * @method date                  getStartDate()         Returns the current record's "start_date" value
 * @method date                  getEndDate()           Returns the current record's "end_date" value
 * @method integer               getActivityId()        Returns the current record's "activity_id" value
 * @method DealerServicesDialogs setId()                Sets the current record's "id" value
 * @method DealerServicesDialogs setYear()              Sets the current record's "year" value
 * @method DealerServicesDialogs setHeader()            Sets the current record's "header" value
 * @method DealerServicesDialogs setDescription()       Sets the current record's "description" value
 * @method DealerServicesDialogs setConfirmBt1Left()    Sets the current record's "confirm_bt1_left" value
 * @method DealerServicesDialogs setConfirmBt1Right()   Sets the current record's "confirm_bt1_right" value
 * @method DealerServicesDialogs setConfirmBt2Left()    Sets the current record's "confirm_bt2_left" value
 * @method DealerServicesDialogs setConfirmBt2Right()   Sets the current record's "confirm_bt2_right" value
 * @method DealerServicesDialogs setConfirmMsg()        Sets the current record's "confirm_msg" value
 * @method DealerServicesDialogs setWidth()             Sets the current record's "width" value
 * @method DealerServicesDialogs setLeftPos()           Sets the current record's "left_pos" value
 * @method DealerServicesDialogs setTemplate()          Sets the current record's "template" value
 * @method DealerServicesDialogs setStatus()            Sets the current record's "status" value
 * @method DealerServicesDialogs setStartDate()         Sets the current record's "start_date" value
 * @method DealerServicesDialogs setEndDate()           Sets the current record's "end_date" value
 * @method DealerServicesDialogs setActivityId()        Sets the current record's "activity_id" value
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseDealerServicesDialogs extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('dealer_services_dialogs');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('header', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('header_dialog', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('description', 'clob', null, array(
             'type' => 'clob',
             'notnull' => false,
             ));
        $this->hasColumn('confirm_bt1_left', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('confirm_bt1_right', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('confirm_bt2_left', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('confirm_bt2_right', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('confirm_msg', 'clob', null, array(
             'type' => 'clob',
             'notnull' => false,
             ));
        $this->hasColumn('width', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('left_pos', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('template', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('status', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => false,
             'default' => false,
             ));
        $this->hasColumn('start_date', 'date', null, array(
             'type' => 'date',
             'notnull' => true,
             ));
        $this->hasColumn('end_date', 'date', null, array(
             'type' => 'date',
             'notnull' => true,
             ));
        $this->hasColumn('without_dates', 'boolean', null, array(
            'type' => 'boolean',
            'notnull' => true,
        ));
        $this->hasColumn('activity_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('success_msg', 'clob', null, array(
             'type' => 'clob',
             'notnull' => false,
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

        $this->hasOne('DealersServicesDialogTemplates', array(
             'local' => 'template',
             'foreign' => 'id'));       

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}