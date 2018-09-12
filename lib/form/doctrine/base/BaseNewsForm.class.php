<?php

/**
 * News form base class.
 *
 * @method News getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseNewsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'name'         => new sfWidgetFormInputText(),
      'announcement' => new sfWidgetFormTextarea(),
      'img_small'    => new sfWidgetFormInputText(),
      'img_big'      => new sfWidgetFormInputText(),
      'text'         => new sfWidgetFormTextarea(),
      'date_of_add'  => new sfWidgetFormDate(),
      //'user_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'status'       => new sfWidgetFormInputCheckbox(),
      'is_important' => new sfWidgetFormInputCheckbox(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'         => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'announcement' => new sfValidatorString(),
      'img_small'    => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'img_big'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'text'         => new sfValidatorString(array('required' => true)),
      'date_of_add'  => new sfValidatorDate(),
      //'user_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'status'       => new sfValidatorBoolean(array('required' => false)),
      'is_important' => new sfValidatorBoolean(array('required' => false)),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('news[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'News';
  }

}
