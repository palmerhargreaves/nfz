<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('AgreementModelsPeriods', 'doctrine');

/**
 * BaseAgreementModelsPeriods
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property date $period_from_date
 * @property date $period_to_date
 * 
 * @method integer                getId()               Returns the current record's "id" value
 * @method date                   getPeriodFromDate()   Returns the current record's "period_from_date" value
 * @method date                   getPeriodToDate()     Returns the current record's "period_to_date" value
 * @method AgreementModelsPeriods setId()               Sets the current record's "id" value
 * @method AgreementModelsPeriods setPeriodFromDate()   Sets the current record's "period_from_date" value
 * @method AgreementModelsPeriods setPeriodToDate()     Sets the current record's "period_to_date" value
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAgreementModelsPeriods extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('agreement_models_periods');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('period_from_date', 'date', null, array(
             'type' => 'date',
             'notnull' => true,
             ));
        $this->hasColumn('period_to_date', 'date', null, array(
             'type' => 'date',
             'notnull' => true,
             ));

        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();

        $this->hasMany('AgreementModelsPeriodsStats as ModelPeriodsStats', array(
            'local' => 'id',
            'foreign' => 'model_period_id',
            'cascade' => array(
                0 => 'delete',
            )));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}