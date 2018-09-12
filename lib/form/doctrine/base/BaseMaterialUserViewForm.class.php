<?php

/**
 * MaterialUserView form base class.
 *
 * @method MaterialUserView getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMaterialUserViewForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'user_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'material_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Material'), 'add_empty' => false)),
      'created_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'material_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Material'))),
      'created_at'  => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'MaterialUserView', 'column' => array('user_id', 'material_id')))
    );

    $this->widgetSchema->setNameFormat('material_user_view[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MaterialUserView';
  }

}
