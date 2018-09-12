<?php

/**
 * Dialogs form base class.
 *
 * @method Dialogs getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDialogsForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'header' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextarea(),
            'start' => new sfWidgetFormDateTime(),
            'end' => new sfWidgetFormDateTime(),
            'width' => new sfWidgetFormInputText(),
            'left_pos' => new sfWidgetFormInputText(),
            'top_pos' => new sfWidgetFormInputText(),
            'status' => new sfWidgetFormInputCheckbox(),
            'on_who_just_registered' => new sfWidgetFormInputCheckbox(),
            'created_at' => new sfWidgetFormDateTime(),
            'updated_at' => new sfWidgetFormDateTime(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'header' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
            'description' => new sfValidatorString(array('required' => true)),
            'start' => new sfValidatorDateTime(),
            'end' => new sfValidatorDateTime(),
            'width' => new sfValidatorString(array('required' => false)),
            'left_pos' => new sfValidatorString(array('required' => false)),
            'top_pos' => new sfValidatorString(array('required' => false)),
            'status' => new sfValidatorBoolean(array('required' => false)),
            'on_who_just_registered' => new sfValidatorBoolean(array('required' => false)),
            'created_at' => new sfValidatorDateTime(),
            'updated_at' => new sfValidatorDateTime(),
        ));

        $this->widgetSchema->setNameFormat('dialogs[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Dialogs';
    }

}
