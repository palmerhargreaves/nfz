<?php

/**
 * MaterialSource form base class.
 *
 * @method MaterialSource getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMaterialSourceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'material_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Material'), 'add_empty' => false)),
      'name'        => new sfWidgetFormInputText(),
      'file'        => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'material_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Material'))),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'file'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('material_source[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MaterialSource';
  }

}
