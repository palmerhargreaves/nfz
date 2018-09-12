<?php

/**
 * LogEntry form base class.
 *
 * @method LogEntry getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogEntryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'login'           => new sfWidgetFormInputText(),
      'title'           => new sfWidgetFormInputText(),
      'description'     => new sfWidgetFormTextarea(),
      'icon'            => new sfWidgetFormInputText(),
      'object_id'       => new sfWidgetFormInputText(),
      'object_type'     => new sfWidgetFormInputText(),
      'module_id'       => new sfWidgetFormInputText(),
      'action'          => new sfWidgetFormInputText(),
      'importance'      => new sfWidgetFormInputCheckbox(),
      'dealer_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => false)),
      'message_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Message'), 'add_empty' => false)),
      'private_user_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PrivateUser'), 'add_empty' => true)),
      'created_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'login'           => new sfValidatorString(array('max_length' => 255)),
      'title'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description'     => new sfValidatorString(),
      'icon'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'object_id'       => new sfValidatorInteger(array('required' => false)),
      'object_type'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'module_id'       => new sfValidatorInteger(array('required' => false)),
      'action'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'importance'      => new sfValidatorBoolean(array('required' => false)),
      'dealer_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'required' => false)),
      'message_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Message'), 'required' => false)),
      'private_user_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PrivateUser'), 'required' => false)),
      'created_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('log_entry[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogEntry';
  }

}
