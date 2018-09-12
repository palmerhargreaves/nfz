<?php

/**
 * Message form base class.
 *
 * @method Message getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMessageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'discussion_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Discussion'), 'add_empty' => false)),
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'private_user_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PrivateUser'), 'add_empty' => true)),
      'user_name'       => new sfWidgetFormInputText(),
      'text'            => new sfWidgetFormTextarea(),
      'system'          => new sfWidgetFormInputCheckbox(),
      'created_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'discussion_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Discussion'))),
      'user_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
      'private_user_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PrivateUser'), 'required' => false)),
      'user_name'       => new sfValidatorString(array('max_length' => 255)),
      'text'            => new sfValidatorString(),
      'system'          => new sfValidatorBoolean(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('message[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Message';
  }

}
