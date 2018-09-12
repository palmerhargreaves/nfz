<?php

/**
 * DealerServicesDialogs form base class.
 *
 * @method DealerServicesDialogs getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDealerServicesDialogsForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'header' => new sfWidgetFormInputText(),
            'header_dialog' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextarea(),
            'confirm_msg' => new sfWidgetFormTextarea(),
            'success_msg' => new sfWidgetFormTextarea(),
            'width' => new sfWidgetFormInputText(),
            'left_pos' => new sfWidgetFormInputText(),
            'template' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'DealersServicesDialogTemplates')),
            'start_date' => new sfWidgetFormDateTime(),
            'end_date' => new sfWidgetFormDateTime(),
            'status' => new sfWidgetFormInputCheckbox(),
            'without_dates' => new sfWidgetFormInputCheckbox(),
            'activity_id' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'Activity')),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'header' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
            'header_dialog' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
            'description' => new sfValidatorString(array('required' => true)),
            'confirm_msg' => new sfValidatorString(array('required' => true)),
            'success_msg' => new sfValidatorString(array('required' => true)),
            'width' => new sfValidatorString(array('required' => false)),
            'left_pos' => new sfValidatorString(array('required' => false)),
            'template' => new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DealersServicesDialogTemplates', 'required' => true)),
            'start_date' => new sfValidatorDateTime(array('required' => false)),
            'end_date' => new sfValidatorDateTime(array('required' => false)),
            'status' => new sfValidatorBoolean(array('required' => false)),
            'without_dates' => new sfValidatorBoolean(array('required' => false)),
            'activity_id' => new sfValidatorDoctrineChoice(array('model' => 'Activity')),
        ));

        $this->widgetSchema->setNameFormat('dealer_services_dialogs[%s]');
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'DealerServicesDialogs';
    }

}
