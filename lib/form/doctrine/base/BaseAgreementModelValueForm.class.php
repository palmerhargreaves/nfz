<?php

/**
 * AgreementModelValue form base class.
 *
 * @method AgreementModelValue getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelValueForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'       => new sfWidgetFormInputHidden(),
      'model_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Model'), 'add_empty' => false)),
      'field_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Field'), 'add_empty' => false)),
      'value'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'model_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Model'))),
      'field_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Field'))),
      'value'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_value[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelValue';
  }

}
