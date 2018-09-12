<?php

/**
 * AgreementModelReport filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelReportFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'model_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Model'), 'add_empty' => true)),
      'financial_docs_file'     => new sfWidgetFormFilterInput(),
      'additional_file'         => new sfWidgetFormFilterInput(),
      'agreement_comments'      => new sfWidgetFormFilterInput(),
      'agreement_comments_file' => new sfWidgetFormFilterInput(),
      'decline_reason_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeclineReason'), 'add_empty' => true)),
      'accept_date'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'status'                  => new sfWidgetFormChoice(array('choices' => array('' => '', 'wait' => 'wait', 'wait_specialist' => 'wait_specialist', 'accepted' => 'accepted', 'declined' => 'declined', 'not_sent' => 'not_sent'))),
      'created_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'model_id'                => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Model'), 'column' => 'id')),
      'financial_docs_file'     => new sfValidatorPass(array('required' => false)),
      'additional_file'         => new sfValidatorPass(array('required' => false)),
      'agreement_comments'      => new sfValidatorPass(array('required' => false)),
      'agreement_comments_file' => new sfValidatorPass(array('required' => false)),
      'decline_reason_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DeclineReason'), 'column' => 'id')),
      'accept_date'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'status'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('wait' => 'wait', 'wait_specialist' => 'wait_specialist', 'accepted' => 'accepted', 'declined' => 'declined', 'not_sent' => 'not_sent'))),
      'created_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_report_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModelReport';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'model_id'                => 'ForeignKey',
      'financial_docs_file'     => 'Text',
      'additional_file'         => 'Text',
      'agreement_comments'      => 'Text',
      'agreement_comments_file' => 'Text',
      'decline_reason_id'       => 'ForeignKey',
      'accept_date'             => 'Date',
      'status'                  => 'Enum',
      'created_at'              => 'Date',
      'updated_at'              => 'Date',
    );
  }
}
