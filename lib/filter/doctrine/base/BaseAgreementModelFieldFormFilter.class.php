<?php

/**
 * AgreementModelField filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelFieldFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'model_type_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'), 'add_empty' => true)),
      'name'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'identifier'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type'              => new sfWidgetFormChoice(array('choices' => array('' => '', 'string' => 'string', 'date' => 'date', 'select' => 'select', 'period' => 'period'))),
      'units'             => new sfWidgetFormFilterInput(),
      'format_hint'       => new sfWidgetFormFilterInput(),
      'format_expression' => new sfWidgetFormFilterInput(),
      'required'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'right_format'      => new sfWidgetFormFilterInput(),
      'sort'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'list'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'model_type_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ModelType'), 'column' => 'id')),
      'name'              => new sfValidatorPass(array('required' => false)),
      'identifier'        => new sfValidatorPass(array('required' => false)),
      'type'              => new sfValidatorChoice(array('required' => false, 'choices' => array('string' => 'string', 'date' => 'date', 'select' => 'select', 'period' => 'period'))),
      'units'             => new sfValidatorPass(array('required' => false)),
      'format_hint'       => new sfValidatorPass(array('required' => false)),
      'format_expression' => new sfValidatorPass(array('required' => false)),
      'required'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'right_format'      => new sfValidatorPass(array('required' => false)),
      'sort'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'list'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_field_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelField';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'model_type_id'     => 'ForeignKey',
      'name'              => 'Text',
      'identifier'        => 'Text',
      'type'              => 'Enum',
      'units'             => 'Text',
      'format_hint'       => 'Text',
      'format_expression' => 'Text',
      'required'          => 'Boolean',
      'right_format'      => 'Text',
      'sort'              => 'Number',
      'list'              => 'Text',
    );
  }
}
