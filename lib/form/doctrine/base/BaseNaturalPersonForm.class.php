<?php

/**
 * NaturalPerson form base class.
 *
 * @method NaturalPerson getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseNaturalPersonForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'firstname'   => new sfWidgetFormInputText(),
      'surname'     => new sfWidgetFormInputText(),
      'patronym'    => new sfWidgetFormInputText(),
      'team_id'     => new sfWidgetFormInputText(),
      'dealer_id'   => new sfWidgetFormInputText(),
      'importer_id' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'firstname'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'surname'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'patronym'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'team_id'     => new sfValidatorInteger(array('required' => false)),
      'dealer_id'   => new sfValidatorInteger(array('required' => false)),
      'importer_id' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('natural_person[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'NaturalPerson';
  }

}
