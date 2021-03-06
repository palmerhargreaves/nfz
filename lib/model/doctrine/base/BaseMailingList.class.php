<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('MailingList', 'doctrine');

/**
 * BaseMailingList
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $dealer_id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $phone
 * @property string $email
 * @property timestamp $last_visit_date
 *
 * @method integer     getId()              Returns the current record's "id" value
 * @method integer     getDealerId()        Returns the current record's "dealer_id" value
 * @method string      getFirstName()       Returns the current record's "first_name" value
 * @method string      getLastName()        Returns the current record's "last_name" value
 * @method string      getMiddleName()      Returns the current record's "middle_name" value
 * @method string      getPhone()           Returns the current record's "phone" value
 * @method string      getEmail()           Returns the current record's "email" value
 * @method timestamp   getLastVisitDate()   Returns the current record's "last_visit_date" value
 * @method MailingList setId()              Sets the current record's "id" value
 * @method MailingList setDealerId()        Sets the current record's "dealer_id" value
 * @method MailingList setFirstName()       Sets the current record's "first_name" value
 * @method MailingList setLastName()        Sets the current record's "last_name" value
 * @method MailingList setMiddleName()      Sets the current record's "middle_name" value
 * @method MailingList setPhone()           Sets the current record's "phone" value
 * @method MailingList setEmail()           Sets the current record's "email" value
 * @method MailingList setLastVisitDate()   Sets the current record's "last_visit_date" value
 *
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseMailingList extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('mailing_list');
        $this->hasColumn('id', 'integer', null, array(
            'type' => 'integer',
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('dealer_id', 'integer', null, array(
            'type' => 'integer',
            'notnull' => true,
        ));
        $this->hasColumn('first_name', 'string', 255, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('last_name', 'string', 255, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('middle_name', 'string', 255, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('firm_name', 'string', 255, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('opf', 'string', 255, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('gender', 'string', 10, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 10,
        ));
        $this->hasColumn('phone', 'string', 255, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('email', 'string', 255, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('last_visit_date', 'string', null, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('last_upload_data', 'string', null, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 255,
        ));
        $this->hasColumn('vin', 'string', null, array(
            'type' => 'string',
            'notnull' => true,
            'length' => 17,
        ));
        $this->hasColumn('added_date', 'timestamp', null, array(
            'type' => 'timestamp',
            'notnull' => true,
        ));
        $this->hasColumn('model', 'string', null, array(
            'type' => 'string',
            'notnull' => true,
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
            'foreign' => 'id')
        );

    }
}