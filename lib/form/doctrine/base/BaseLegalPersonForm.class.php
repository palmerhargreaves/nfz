<?php

/**
 * LegalPerson form base class.
 *
 * @method LegalPerson getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLegalPersonForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'name'                  => new sfWidgetFormInputText(),
      'legal_address'         => new sfWidgetFormInputText(),
      'INN'                   => new sfWidgetFormInputText(),
      'KPP'                   => new sfWidgetFormInputText(),
      'OKPO'                  => new sfWidgetFormInputText(),
      'transactional_account' => new sfWidgetFormInputText(),
      'correspondent_account' => new sfWidgetFormInputText(),
      'BIK'                   => new sfWidgetFormInputText(),
      'bank_name'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                  => new sfValidatorString(array('max_length' => 255)),
      'legal_address'         => new sfValidatorString(array('max_length' => 255)),
      'INN'                   => new sfValidatorString(array('max_length' => 128)),
      'KPP'                   => new sfValidatorString(array('max_length' => 128)),
      'OKPO'                  => new sfValidatorString(array('max_length' => 128)),
      'transactional_account' => new sfValidatorString(array('max_length' => 128)),
      'correspondent_account' => new sfValidatorString(array('max_length' => 128)),
      'BIK'                   => new sfValidatorString(array('max_length' => 128)),
      'bank_name'             => new sfValidatorString(array('max_length' => 255)),
    ));

    $this->widgetSchema->setNameFormat('legal_person[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LegalPerson';
  }

}
