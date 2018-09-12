<?php

/**
 * ActivityInfo form base class.
 *
 * @method ActivityInfo getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseActivityInfoForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => false)),
      'icon'        => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
      'text'        => new sfWidgetFormTextarea(),
      'sort'        => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'))),
      'icon'        => new sfValidatorString(array('max_length' => 255)),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'text'        => new sfValidatorString(),
      'sort'        => new sfValidatorInteger(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('activity_info[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ActivityInfo';
  }

}
