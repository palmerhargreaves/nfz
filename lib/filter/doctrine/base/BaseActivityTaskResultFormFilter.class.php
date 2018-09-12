<?php

/**
 * ActivityTaskResult filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseActivityTaskResultFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dealer_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => true)),
      'task_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityTask'), 'add_empty' => true)),
      'done'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'dealer_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Dealer'), 'column' => 'id')),
      'task_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ActivityTask'), 'column' => 'id')),
      'done'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('activity_task_result_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ActivityTaskResult';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'dealer_id'  => 'ForeignKey',
      'task_id'    => 'ForeignKey',
      'done'       => 'Boolean',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    );
  }
}
