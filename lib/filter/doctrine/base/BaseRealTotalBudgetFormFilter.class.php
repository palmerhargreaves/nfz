<?php

/**
 * RealTotalBudget filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseRealTotalBudgetFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dealer_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => true)),
      'year'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'quarter'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sum'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'dealer_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Dealer'), 'column' => 'id')),
      'year'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'quarter'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sum'       => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('real_total_budget_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'RealTotalBudget';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'dealer_id' => 'ForeignKey',
      'year'      => 'Number',
      'quarter'   => 'Number',
      'sum'       => 'Number',
    );
  }
}
