<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('MaterialCategory', 'doctrine');

/**
 * BaseMaterialCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property Doctrine_Collection $Materials
 * 
 * @method integer             getId()        Returns the current record's "id" value
 * @method string              getName()      Returns the current record's "name" value
 * @method Doctrine_Collection getMaterials() Returns the current record's "Materials" collection
 * @method MaterialCategory    setId()        Sets the current record's "id" value
 * @method MaterialCategory    setName()      Sets the current record's "name" value
 * @method MaterialCategory    setMaterials() Sets the current record's "Materials" collection
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseMaterialCategory extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('material_category');
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

        $this->hasColumn('category_order', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));


        $this->index('name', array(
             'fields' => 
             array(
              0 => 'name',
             ),
             ));
        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
        $this->option('orderBy', 'name asc');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Material as Materials', array(
             'local' => 'id',
             'foreign' => 'category_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}