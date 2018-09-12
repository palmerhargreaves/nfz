<?php

/**
 * TempFile form base class.
 *
 * @method TempFile getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTempFileForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'user_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
            'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => false)),
            'file' => new sfWidgetFormInputText(),
            'file_object_type' => new sfWidgetFormInputText(),
            'file_type' => new sfWidgetFormInputText(),
            'file_size' => new sfWidgetFormInputText(),
            'created_at' => new sfWidgetFormDateTime(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'user_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
            'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'required' => false)),
            'file' => new sfValidatorString(array('max_length' => 255)),
            'file_object_type' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'file_type' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'file_size' => new sfValidatorString(array('max_length' => 11, 'required' => false)),
            'created_at' => new sfValidatorDateTime(),
        ));

        $this->widgetSchema->setNameFormat('temp_file[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'TempFile';
    }

}
