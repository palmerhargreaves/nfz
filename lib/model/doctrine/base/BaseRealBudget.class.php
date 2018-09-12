<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('RealBudget', 'doctrine');

/**
 * BaseRealBudget
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $dealer_id
 * @property integer $year
 * @property integer $quarter
 * @property decimal $sum
 * @property integer $module_id
 * @property integer $object_id
 * @property Dealer $Dealer
 * @property ActivityModule $Module
 * 
 * @method integer        getId()        Returns the current record's "id" value
 * @method integer        getDealerId()  Returns the current record's "dealer_id" value
 * @method integer        getYear()      Returns the current record's "year" value
 * @method integer        getQuarter()   Returns the current record's "quarter" value
 * @method decimal        getSum()       Returns the current record's "sum" value
 * @method integer        getModuleId()  Returns the current record's "module_id" value
 * @method integer        getObjectId()  Returns the current record's "object_id" value
 * @method Dealer         getDealer()    Returns the current record's "Dealer" value
 * @method ActivityModule getModule()    Returns the current record's "Module" value
 * @method RealBudget     setId()        Sets the current record's "id" value
 * @method RealBudget     setDealerId()  Sets the current record's "dealer_id" value
 * @method RealBudget     setYear()      Sets the current record's "year" value
 * @method RealBudget     setQuarter()   Sets the current record's "quarter" value
 * @method RealBudget     setSum()       Sets the current record's "sum" value
 * @method RealBudget     setModuleId()  Sets the current record's "module_id" value
 * @method RealBudget     setObjectId()  Sets the current record's "object_id" value
 * @method RealBudget     setDealer()    Sets the current record's "Dealer" value
 * @method RealBudget     setModule()    Sets the current record's "Module" value
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseRealBudget extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('real_budget');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('dealer_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('year', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('quarter', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));
        $this->hasColumn('sum', 'decimal', null, array(
             'type' => 'decimal',
             'scale' => 2,
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('module_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('object_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));

        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Dealer', array(
             'local' => 'dealer_id',
             'foreign' => 'id'));

        $this->hasOne('ActivityModule as Module', array(
             'local' => 'module_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'updated' => 
             array(
              'disabled' => true,
             ),
             ));
        $this->actAs($timestampable0);
    }
}