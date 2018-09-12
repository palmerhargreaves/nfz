<?php

/**
 * AgreementModel filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'activity_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => true)),
      'dealer_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => true)),
      'name'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'model_type_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ModelType'), 'add_empty' => true)),
      'target'                  => new sfWidgetFormFilterInput(),
      'cost'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'agreement_comments'      => new sfWidgetFormFilterInput(),
      'agreement_comments_file' => new sfWidgetFormFilterInput(),
      'decline_reason_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeclineReason'), 'add_empty' => true)),
      'model_file'              => new sfWidgetFormFilterInput(),
      'status'                  => new sfWidgetFormChoice(array('choices' => array('' => '', 'wait' => 'wait', 'wait_specialist' => 'wait_specialist', 'accepted' => 'accepted', 'declined' => 'declined', 'not_sent' => 'not_sent'))),
      'report_id'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Report'), 'add_empty' => true)),
      'discussion_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Discussion'), 'add_empty' => true)),
      'blank_id'                => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Blank'), 'add_empty' => true)),
      'wait'                    => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'wait_specialist'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'activity_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Activity'), 'column' => 'id')),
      'dealer_id'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Dealer'), 'column' => 'id')),
      'name'                    => new sfValidatorPass(array('required' => false)),
      'model_type_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ModelType'), 'column' => 'id')),
      'target'                  => new sfValidatorPass(array('required' => false)),
      'cost'                    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'agreement_comments'      => new sfValidatorPass(array('required' => false)),
      'agreement_comments_file' => new sfValidatorPass(array('required' => false)),
      'decline_reason_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DeclineReason'), 'column' => 'id')),
      'model_file'              => new sfValidatorPass(array('required' => false)),
      'status'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('wait' => 'wait', 'wait_specialist' => 'wait_specialist', 'accepted' => 'accepted', 'declined' => 'declined', 'not_sent' => 'not_sent'))),
      'report_id'               => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Report'), 'column' => 'id')),
      'discussion_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Discussion'), 'column' => 'id')),
      'blank_id'                => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Blank'), 'column' => 'id')),
      'wait'                    => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'wait_specialist'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('agreement_model_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementModel';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'activity_id'             => 'ForeignKey',
      'dealer_id'               => 'ForeignKey',
      'name'                    => 'Text',
      'model_type_id'           => 'ForeignKey',
      'target'                  => 'Text',
      'cost'                    => 'Number',
      'agreement_comments'      => 'Text',
      'agreement_comments_file' => 'Text',
      'decline_reason_id'       => 'ForeignKey',
      'model_file'              => 'Text',
      'status'                  => 'Enum',
      'report_id'               => 'ForeignKey',
      'discussion_id'           => 'ForeignKey',
      'blank_id'                => 'ForeignKey',
      'wait'                    => 'Boolean',
      'wait_specialist'         => 'Boolean',
      'created_at'              => 'Date',
      'updated_at'              => 'Date',
    );
  }
}
