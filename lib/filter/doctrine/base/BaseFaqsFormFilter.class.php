<?php

/**
 * Faqs filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseFaqsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'question'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'answer'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'image'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'status'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'question'     => new sfValidatorPass(array('required' => true)),
      'answer'       => new sfValidatorPass(array('required' => true)),
      'image'        => new sfValidatorPass(array('required' => false)),
      'user_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'status'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('faqs_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Faqs';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'question'     => 'Text',
      'answer'       => 'Text',
      'image'        => 'Text',
      'user_id'      => 'ForeignKey',
      'status'       => 'Number',
      'created_at'   => 'Date',
      'updated_at'   => 'Date',
    );
  }
}
