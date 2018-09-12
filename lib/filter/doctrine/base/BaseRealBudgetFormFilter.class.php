<?php

/**
 * RealBudget filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseRealBudgetFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dealer_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => true)),
      'year'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'quarter'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sum'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'module_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Module'), 'add_empty' => true)),
      'object_id'  => new sfWidgetFormFilterInput(),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'dealer_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Dealer'), 'column' => 'id')),
      'year'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'quarter'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sum'        => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'module_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Module'), 'column' => 'id')),
      'object_id'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('real_budget_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'RealBudget';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'dealer_id'  => 'ForeignKey',
      'year'       => 'Number',
      'quarter'    => 'Number',
      'sum'        => 'Number',
      'module_id'  => 'ForeignKey',
      'object_id'  => 'Number',
      'created_at' => 'Date',
    );
  }
}
