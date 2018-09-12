<?php

/**
 * RealBudget form base class.
 *
 * @method RealBudget getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseRealBudgetForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'dealer_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => false)),
      'year'       => new sfWidgetFormInputText(),
      'quarter'    => new sfWidgetFormInputText(),
      'sum'        => new sfWidgetFormInputText(),
      'module_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Module'), 'add_empty' => false)),
      'object_id'  => new sfWidgetFormInputText(),
      'created_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'dealer_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'))),
      'year'       => new sfValidatorInteger(),
      'quarter'    => new sfValidatorInteger(),
      'sum'        => new sfValidatorNumber(array('required' => false)),
      'module_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Module'))),
      'object_id'  => new sfValidatorInteger(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('real_budget[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'RealBudget';
  }

}
