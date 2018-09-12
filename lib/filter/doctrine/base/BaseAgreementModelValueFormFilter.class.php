<?php

/**
 * AgreementModelValue filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelValueFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'model_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Model'), 'add_empty' => true)),
      'field_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Field'), 'add_empty' => true)),
      'value'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'model_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Model'), 'column' => 'id')),
      'field_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Field'), 'column' => 'id')),
      'value'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_value_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelValue';
  }

  public function getFields()
  {
    return array(
      'id'       => 'Number',
      'model_id' => 'ForeignKey',
      'field_id' => 'ForeignKey',
      'value'    => 'Text',
    );
  }
}
