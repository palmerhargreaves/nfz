<?php

/**
 * AgreementModelBlank filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelBlankFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'activity_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => true)),
      'name'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'model_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'), 'add_empty' => true)),
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'activity_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Activity'), 'column' => 'id')),
      'name'          => new sfValidatorPass(array('required' => false)),
      'model_type_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ModelType'), 'column' => 'id')),
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_blank_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelBlank';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'activity_id'   => 'ForeignKey',
      'name'          => 'Text',
      'model_type_id' => 'ForeignKey',
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
    );
  }
}
