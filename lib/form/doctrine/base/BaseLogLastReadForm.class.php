<?php

/**
 * LogLastRead form base class.
 *
 * @method LogLastRead getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogLastReadForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'last_read' => new sfWidgetFormDateTime(),
      'user_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'last_read' => new sfValidatorDateTime(),
      'user_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'LogLastRead', 'column' => array('user_id')))
    );

    $this->widgetSchema->setNameFormat('log_last_read[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogLastRead';
  }

}
