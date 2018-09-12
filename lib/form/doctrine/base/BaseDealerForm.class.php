<?php

/**
 * Dealer form base class.
 *
 * @method Dealer getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDealerForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'number'              => new sfWidgetFormInputText(),
      'name'                => new sfWidgetFormInputText(),
      'address'             => new sfWidgetFormInputText(),
      'phone'               => new sfWidgetFormInputText(),
      'site'                => new sfWidgetFormInputText(),
      'email'               => new sfWidgetFormInputText(),
      'longitude'           => new sfWidgetFormInputText(),
      'latitude'            => new sfWidgetFormInputText(),
      'city_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('City'), 'add_empty' => true)),
      'regional_manager_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('RegionalManager'), 'add_empty' => true)),
      'company_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('LegalPerson'), 'add_empty' => true)),
      'importer_id'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'number'              => new sfValidatorInteger(array('required' => false)),
      'name'                => new sfValidatorString(array('max_length' => 60, 'required' => false)),
      'address'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'phone'               => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'site'                => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'email'               => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'longitude'           => new sfValidatorNumber(array('required' => false)),
      'latitude'            => new sfValidatorNumber(array('required' => false)),
      'city_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('City'), 'required' => false)),
      'regional_manager_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('RegionalManager'), 'required' => false)),
      'company_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('LegalPerson'), 'required' => false)),
      'importer_id'         => new sfValidatorInteger(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Dealer', 'column' => array('number')))
    );

    $this->widgetSchema->setNameFormat('dealer[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Dealer';
  }

}
