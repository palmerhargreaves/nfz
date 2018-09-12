<?php

/**
 * AgreementModelField form base class.
 *
 * @method AgreementModelField getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelFieldForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'model_type_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'), 'add_empty' => false)),
      'name'              => new sfWidgetFormInputText(),
      'identifier'        => new sfWidgetFormInputText(),
      'type'              => new sfWidgetFormChoice(array('choices' => array('string' => 'string', 'date' => 'date', 'select' => 'select', 'period' => 'period'))),
      'units'             => new sfWidgetFormInputText(),
      'format_hint'       => new sfWidgetFormInputText(),
      'format_expression' => new sfWidgetFormInputText(),
      'required'          => new sfWidgetFormInputCheckbox(),
      'right_format'      => new sfWidgetFormInputText(),
      'sort'              => new sfWidgetFormInputText(),
      'list'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'model_type_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'))),
      'name'              => new sfValidatorString(array('max_length' => 255)),
      'identifier'        => new sfValidatorString(array('max_length' => 255)),
      'type'              => new sfValidatorChoice(array('choices' => array(0 => 'string', 1 => 'date', 2 => 'select', 3 => 'period'))),
      'units'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'format_hint'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'format_expression' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'required'          => new sfValidatorBoolean(array('required' => false)),
      'right_format'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sort'              => new sfValidatorInteger(array('required' => false)),
      'list'              => new sfValidatorString(array('max_length' => 255)),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_field[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelField';
  }

}
