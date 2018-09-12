<?php

/**
 * AgreementModelBlank form base class.
 *
 * @method AgreementModelBlank getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelBlankForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'activity_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => false)),
      'name'          => new sfWidgetFormInputText(),
      'model_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'), 'add_empty' => false)),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'activity_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'))),
      'name'          => new sfValidatorString(array('max_length' => 255)),
      'model_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'))),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_blank[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelBlank';
  }

}
