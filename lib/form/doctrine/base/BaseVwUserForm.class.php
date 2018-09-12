<?php

/**
 * VwUser form base class.
 *
 * @method VwUser getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVwUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'email'     => new sfWidgetFormInputText(),
      'person_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Person'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'email'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'person_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Person'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('vw_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VwUser';
  }

}
