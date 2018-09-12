<?php

/**
 * AgreementModelType form base class.
 *
 * @method AgreementModelType getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'name'                     => new sfWidgetFormInputText(),
      'identifier'               => new sfWidgetFormInputText(),
      'report_field_description' => new sfWidgetFormInputText(),
      'concept'                  => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                     => new sfValidatorString(array('max_length' => 255)),
      'identifier'               => new sfValidatorString(array('max_length' => 255)),
      'report_field_description' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'concept'                  => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'AgreementModelType', 'column' => array('name'))),
        new sfValidatorDoctrineUnique(array('model' => 'AgreementModelType', 'column' => array('identifier'))),
      ))
    );

    $this->widgetSchema->setNameFormat('agreement_model_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelType';
  }

}
