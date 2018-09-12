<?php

/**
 * LogEntryRead form base class.
 *
 * @method LogEntryRead getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogEntryReadForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'       => new sfWidgetFormInputHidden(),
      'entry_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Entry'), 'add_empty' => false)),
      'user_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entry_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Entry'))),
      'user_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'LogEntryRead', 'column' => array('user_id', 'entry_id')))
    );

    $this->widgetSchema->setNameFormat('log_entry_read[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogEntryRead';
  }

}
