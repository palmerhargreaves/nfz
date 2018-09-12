<?php

/**
 * DealerBonus filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDealerBonusFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dealer_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => true)),
      'year'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'quarter'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'bonus'     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'comment'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'dealer_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Dealer'), 'column' => 'id')),
      'year'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'quarter'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bonus'     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'comment'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dealer_bonus_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DealerBonus';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'dealer_id' => 'ForeignKey',
      'year'      => 'Number',
      'quarter'   => 'Number',
      'bonus'     => 'Boolean',
      'comment'   => 'Text',
    );
  }
}
