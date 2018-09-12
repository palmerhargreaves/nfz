<?php

/**
 * Faqs form base class.
 *
 * @method News getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseFaqsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'question'         => new sfWidgetFormTextarea(),
      'answer' => new sfWidgetFormTextarea(),
      'image'    => new sfWidgetFormInputText(),
      'status'       => new sfWidgetFormInputCheckbox(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'question'         => new sfValidatorString(array('required' => true)),
      'answer' => new sfValidatorString(array('required' => true)),
      'image'    => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'status'       => new sfValidatorBoolean(array('required' => false)),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('faqs[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Faqs';
  }

}
