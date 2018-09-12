<?php

/**
 * AgreementModel form base class.
 *
 * @method AgreementModel getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => false)),
            'dealer_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => false)),
            'name' => new sfWidgetFormInputText(),
            'model_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'), 'add_empty' => false)),
            'target' => new sfWidgetFormInputText(),
            'cost' => new sfWidgetFormInputText(),
            'agreement_comments' => new sfWidgetFormInputText(),
            'agreement_comments_file' => new sfWidgetFormInputText(),
            'decline_reason_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeclineReason'), 'add_empty' => true)),
            'accept_in_model' => new sfWidgetFormInputText(),
            'model_file' => new sfWidgetFormInputText(),
            'status' => new sfWidgetFormChoice(array('choices' => array('wait' => 'wait', 'wait_specialist' => 'wait_specialist', 'accepted' => 'accepted', 'declined' => 'declined', 'not_sent' => 'not_sent'))),
            'report_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Report'), 'add_empty' => true)),
            'discussion_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Discussion'), 'add_empty' => true)),
            'blank_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Blank'), 'add_empty' => true)),
            'wait' => new sfWidgetFormInputCheckbox(),
            'wait_specialist' => new sfWidgetFormInputCheckbox(),
            'no_model_changes' => new sfWidgetFormInputCheckbox(),
            'model_record_file' => new sfWidgetFormInputText(),
            //'model_accepted_in_online_redactor' => new sfWidgetFormInputCheckbox(),
            //'task_id'           		=> new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityTask'), 'add_empty' => false)),
            'editor_link' => new sfWidgetFormInputText(),
            'created_at' => new sfWidgetFormDateTime(),
            'updated_at' => new sfWidgetFormDateTime(),
            'is_valid_data' => new sfWidgetFormInputText()
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'))),
            'dealer_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'))),
            'name' => new sfValidatorString(array('max_length' => 255)),
            'model_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'))),
            'target' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'cost' => new sfValidatorNumber(array('required' => false)),
            'agreement_comments' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'agreement_comments_file' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'decline_reason_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DeclineReason'), 'required' => false)),
            'accept_in_model' => new sfValidatorString(array('max_length' => 5, 'required' => false)),
            'model_file' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'status' => new sfValidatorChoice(array('choices' => array(0 => 'wait', 1 => 'wait_specialist', 2 => 'accepted', 3 => 'declined', 4 => 'not_sent'))),
            'report_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Report'), 'required' => false)),
            'discussion_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Discussion'), 'required' => false)),
            'blank_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Blank'), 'required' => false)),
            'wait' => new sfValidatorBoolean(array('required' => false)),
            'wait_specialist' => new sfValidatorBoolean(array('required' => false)),
            'no_model_changes' => new sfValidatorBoolean(array('required' => false)),
            'model_record_file' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            //'model_accepted_in_online_redactor' => new sfValidatorBoolean(array('required' => false)),
            'editor_link' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'created_at' => new sfValidatorDateTime(),
            'updated_at' => new sfValidatorDateTime(),
            'is_valid_data' => new sfValidatorString(array('max_length' => 10, 'required' => false))
        ));

        $this->widgetSchema->setNameFormat('agreement_model[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'AgreementModel';
    }

}
