<?php

/**
 * Sp1User form base class.
 *
 * @method Sp1User getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSp1UserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'dealer_number' => new sfWidgetFormInputText(),
      'company'       => new sfWidgetFormInputText(),
      'post'          => new sfWidgetFormInputText(),
      'family'        => new sfWidgetFormInputText(),
      'name'          => new sfWidgetFormInputText(),
      'email'         => new sfWidgetFormInputText(),
      'phone'         => new sfWidgetFormInputText(),
      'mobile_phone'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'dealer_number' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'company'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'post'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'family'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'name'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'email'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'phone'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'mobile_phone'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sp1_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Sp1User';
  }

}
