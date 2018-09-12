<?php

/**
 * DealerBonus form base class.
 *
 * @method DealerBonus getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDealerBonusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'dealer_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => false)),
      'year'      => new sfWidgetFormInputText(),
      'quarter'   => new sfWidgetFormInputText(),
      'bonus'     => new sfWidgetFormInputCheckbox(),
      'comment'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'dealer_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'))),
      'year'      => new sfValidatorInteger(),
      'quarter'   => new sfValidatorInteger(),
      'bonus'     => new sfValidatorBoolean(array('required' => false)),
      'comment'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dealer_bonus[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DealerBonus';
  }

}
