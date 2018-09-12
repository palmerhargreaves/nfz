<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('LogEntry', 'doctrine');

/**
 * BaseLogEntry
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $user_id
 * @property string $login
 * @property string $title
 * @property clob $description
 * @property string $icon
 * @property integer $object_id
 * @property string $object_type
 * @property integer $module_id
 * @property string $action
 * @property boolean $importance
 * @property integer $dealer_id
 * @property integer $message_id
 * @property integer $private_user_id
 * @property User $User
 * @property Dealer $Dealer
 * @property Message $Message
 * @property User $PrivateUser
 * @property Doctrine_Collection $UserReads
 * 
 * @method integer             getId()              Returns the current record's "id" value
 * @method integer             getUserId()          Returns the current record's "user_id" value
 * @method string              getLogin()           Returns the current record's "login" value
 * @method string              getTitle()           Returns the current record's "title" value
 * @method clob                getDescription()     Returns the current record's "description" value
 * @method string              getIcon()            Returns the current record's "icon" value
 * @method integer             getObjectId()        Returns the current record's "object_id" value
 * @method string              getObjectType()      Returns the current record's "object_type" value
 * @method integer             getModuleId()        Returns the current record's "module_id" value
 * @method string              getAction()          Returns the current record's "action" value
 * @method boolean             getImportance()      Returns the current record's "importance" value
 * @method integer             getDealerId()        Returns the current record's "dealer_id" value
 * @method integer             getMessageId()       Returns the current record's "message_id" value
 * @method integer             getPrivateUserId()   Returns the current record's "private_user_id" value
 * @method User                getUser()            Returns the current record's "User" value
 * @method Dealer              getDealer()          Returns the current record's "Dealer" value
 * @method Message             getMessage()         Returns the current record's "Message" value
 * @method User                getPrivateUser()     Returns the current record's "PrivateUser" value
 * @method Doctrine_Collection getUserReads()       Returns the current record's "UserReads" collection
 * @method LogEntry            setId()              Sets the current record's "id" value
 * @method LogEntry            setUserId()          Sets the current record's "user_id" value
 * @method LogEntry            setLogin()           Sets the current record's "login" value
 * @method LogEntry            setTitle()           Sets the current record's "title" value
 * @method LogEntry            setDescription()     Sets the current record's "description" value
 * @method LogEntry            setIcon()            Sets the current record's "icon" value
 * @method LogEntry            setObjectId()        Sets the current record's "object_id" value
 * @method LogEntry            setObjectType()      Sets the current record's "object_type" value
 * @method LogEntry            setModuleId()        Sets the current record's "module_id" value
 * @method LogEntry            setAction()          Sets the current record's "action" value
 * @method LogEntry            setImportance()      Sets the current record's "importance" value
 * @method LogEntry            setDealerId()        Sets the current record's "dealer_id" value
 * @method LogEntry            setMessageId()       Sets the current record's "message_id" value
 * @method LogEntry            setPrivateUserId()   Sets the current record's "private_user_id" value
 * @method LogEntry            setUser()            Sets the current record's "User" value
 * @method LogEntry            setDealer()          Sets the current record's "Dealer" value
 * @method LogEntry            setMessage()         Sets the current record's "Message" value
 * @method LogEntry            setPrivateUser()     Sets the current record's "PrivateUser" value
 * @method LogEntry            setUserReads()       Sets the current record's "UserReads" collection
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLogEntry extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('log');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('login', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('description', 'clob', null, array(
             'type' => 'clob',
             'notnull' => true,
             ));
        $this->hasColumn('icon', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('object_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('object_type', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('module_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('action', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('importance', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => false,
             ));
        $this->hasColumn('dealer_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('message_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('private_user_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             'default' => 0,
             ));


        $this->index('user', array(
             'fields' => 
             array(
              0 => 'user_id',
              1 => 'object_id',
             ),
             ));
        $this->index('object', array(
             'fields' => 
             array(
              0 => 'object_id',
             ),
             ));
        $this->index('object_type', array(
             'fields' => 
             array(
              0 => 'object_type',
             ),
             ));
        $this->index('created_at', array(
             'fields' => 
             array(
              0 => 'created_at',
              1 => 'message_id',
             ),
             ));
        $this->index('content', array(
             'fields' => 
             array(
              0 => 'title',
              1 => 'description',
             ),
             'type' => 'fulltext',
             ));
        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('User', array(
             'local' => 'user_id',
             'foreign' => 'id'));

        $this->hasOne('Dealer', array(
             'local' => 'dealer_id',
             'foreign' => 'id'));

        $this->hasOne('Message', array(
             'local' => 'message_id',
             'foreign' => 'id'));

        $this->hasOne('User as PrivateUser', array(
             'local' => 'private_user_id',
             'foreign' => 'id'));

        $this->hasMany('LogEntryRead as UserReads', array(
             'local' => 'id',
             'foreign' => 'entry_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'updated' => 
             array(
              'disabled' => true,
             ),
             ));
        $this->actAs($timestampable0);
    }
}