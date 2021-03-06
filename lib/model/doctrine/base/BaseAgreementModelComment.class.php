<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('AgreementModelComment', 'doctrine');

/**
 * BaseAgreementModelComment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $model_id
 * @property AgreementModel $Model
 * @property User $User
 * 
 * @method integer               getModelId()  Returns the current record's "model_id" value
 * @method AgreementModel        getModel()    Returns the current record's "Model" value
 * @method User                  getUser()     Returns the current record's "User" value
 * @method AgreementModelComment setModelId()  Sets the current record's "model_id" value
 * @method AgreementModelComment setModel()    Sets the current record's "Model" value
 * @method AgreementModelComment setUser()     Sets the current record's "User" value
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAgreementModelComment extends AgreementComment
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('agreement_model_comment');
        $this->hasColumn('model_id', 'integer', null, array(
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
        $this->hasOne('AgreementModel as Model', array(
             'local' => 'model_id',
             'foreign' => 'id'));

        $this->hasOne('User', array(
             'local' => 'user_id',
             'foreign' => 'id'));
    }
}