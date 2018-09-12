<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('AgreementDeclineReason', 'doctrine');

/**
 * BaseAgreementDeclineReason
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property Doctrine_Collection $Models
 * 
 * @method integer                getId()     Returns the current record's "id" value
 * @method string                 getName()   Returns the current record's "name" value
 * @method Doctrine_Collection    getModels() Returns the current record's "Models" collection
 * @method AgreementDeclineReason setId()     Sets the current record's "id" value
 * @method AgreementDeclineReason setName()   Sets the current record's "name" value
 * @method AgreementDeclineReason setModels() Sets the current record's "Models" collection
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAgreementDeclineReason extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('agreement_decline_reason');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));

        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('AgreementModel as Models', array(
             'local' => 'id',
             'foreign' => 'decline_reason_id'));
    }
}