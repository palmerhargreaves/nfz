<?php

/**
 * AgreementModelReport form base class.
 *
 * @method AgreementModelReport getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelReportForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'model_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Model'), 'add_empty' => false)),
            //'financial_docs_file' => new sfWidgetFormInputText(),
            //'additional_file' => new sfWidgetFormInputText(),
            'agreement_comments' => new sfWidgetFormTextarea(),
            'agreement_comments_file' => new sfWidgetFormInputText(),
            'decline_reason_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeclineReason'), 'add_empty' => true)),
            'accept_date' => new sfWidgetFormDateTime(),
            'status' => new sfWidgetFormChoice(array('choices' => array('wait' => 'wait', 'wait_specialist' => 'wait_specialist', 'accepted' => 'accepted', 'declined' => 'declined', 'not_sent' => 'not_sent'))),
            'created_at' => new sfWidgetFormDateTime(),
            'updated_at' => new sfWidgetFormDateTime(),
            'is_valid_add_data' => new sfWidgetFormInputText(),
            'is_valid_fin_data' => new sfWidgetFormInputText()
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'model_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Model'))),
            //'financial_docs_file' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            //'additional_file' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'agreement_comments' => new sfValidatorString(array('required' => false)),
            'agreement_comments_file' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'decline_reason_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DeclineReason'), 'required' => false)),
            'accept_date' => new sfValidatorDateTime(array('required' => false)),
            'status' => new sfValidatorChoice(array('choices' => array(0 => 'wait', 1 => 'wait_specialist', 2 => 'accepted', 3 => 'declined', 4 => 'not_sent'))),
            'created_at' => new sfValidatorDateTime(),
            'updated_at' => new sfValidatorDateTime(),
            'is_valid_add_data' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
            'is_valid_fin_data' => new sfValidatorString(array('max_length' => 10, 'required' => false))
        ));

        $this->validatorSchema->setPostValidator(
            new sfValidatorDoctrineUnique(array('model' => 'AgreementModelReport', 'column' => array('model_id')))
        );

        $this->widgetSchema->setNameFormat('agreement_model_report[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'AgreementModelReport';
    }

}
