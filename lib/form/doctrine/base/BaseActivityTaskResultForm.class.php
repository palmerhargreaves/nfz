<?php

/**
 * ActivityTaskResult form base class.
 *
 * @method ActivityTaskResult getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseActivityTaskResultForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'dealer_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => false)),
      'task_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityTask'), 'add_empty' => false)),
      'done'       => new sfWidgetFormInputCheckbox(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'dealer_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'))),
      'task_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityTask'))),
      'done'       => new sfValidatorBoolean(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ActivityTaskResult', 'column' => array('dealer_id', 'task_id')))
    );

    $this->widgetSchema->setNameFormat('activity_task_result[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ActivityTaskResult';
  }

}
