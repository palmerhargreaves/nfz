<?php

/**
 * AgreementModelType filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'identifier'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'report_field_description' => new sfWidgetFormFilterInput(),
      'concept'                  => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'name'                     => new sfValidatorPass(array('required' => false)),
      'identifier'               => new sfValidatorPass(array('required' => false)),
      'report_field_description' => new sfValidatorPass(array('required' => false)),
      'concept'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelType';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'name'                     => 'Text',
      'identifier'               => 'Text',
      'report_field_description' => 'Text',
      'concept'                  => 'Boolean',
    );
  }
}
