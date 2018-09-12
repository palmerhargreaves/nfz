<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('AgreementComment', 'doctrine');

/**
 * BaseAgreementComment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $user_id
 * @property enum $status
 * 
 * @method integer          getId()      Returns the current record's "id" value
 * @method integer          getUserId()  Returns the current record's "user_id" value
 * @method enum             getStatus()  Returns the current record's "status" value
 * @method AgreementComment setId()      Sets the current record's "id" value
 * @method AgreementComment setUserId()  Sets the current record's "user_id" value
 * @method AgreementComment setStatus()  Sets the current record's "status" value
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAgreementComment extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('agreement_comment');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('status', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'wait',
              1 => 'accepted',
              2 => 'declined',
             ),
             'notnull' => true,
             ));


        $this->index('status', array(
             'fields' => 
             array(
              0 => 'status',
             ),
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