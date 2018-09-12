<?php

/**
 * User filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUserFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'group_id'                                    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => true)),
      'email'                                       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'password'                                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'                                        => new sfWidgetFormFilterInput(),
      'surname'                                     => new sfWidgetFormFilterInput(),
      'patronymic'                                  => new sfWidgetFormFilterInput(),
      'company_type'                                => new sfWidgetFormChoice(array('choices' => array('' => '', 'dealer' => 'dealer', 'importer' => 'importer', 'other' => 'other'))),
      'company_name'                                => new sfWidgetFormFilterInput(),
      'post'                                        => new sfWidgetFormFilterInput(),
      'phone'                                       => new sfWidgetFormFilterInput(),
      'mobile'                                      => new sfWidgetFormFilterInput(),
      'recovery_key'                                => new sfWidgetFormFilterInput(),
      'activation_key'                              => new sfWidgetFormFilterInput(),
      'registration_notification'                   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'agreement_notification'                      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'new_agreement_notification'                  => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'final_agreement_notification'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'agreement_report_notification'               => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'new_agreement_report_notification'           => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'final_agreement_report_notification'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'agreement_concept_notification'              => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'new_agreement_concept_notification'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'final_agreement_concept_notification'        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'agreement_concept_report_notification'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'new_agreement_concept_report_notification'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'final_agreement_concept_report_notification' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'dealer_discussion_notification'              => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'model_discussion_notification'               => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'active'                                      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'group_id'                                    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Group'), 'column' => 'id')),
      'email'                                       => new sfValidatorPass(array('required' => false)),
      'password'                                    => new sfValidatorPass(array('required' => false)),
      'name'                                        => new sfValidatorPass(array('required' => false)),
      'surname'                                     => new sfValidatorPass(array('required' => false)),
      'patronymic'                                  => new sfValidatorPass(array('required' => false)),
      'company_type'                                => new sfValidatorChoice(array('required' => false, 'choices' => array('dealer' => 'dealer', 'importer' => 'importer', 'other' => 'other'))),
      'company_name'                                => new sfValidatorPass(array('required' => false)),
      'post'                                        => new sfValidatorPass(array('required' => false)),
      'phone'                                       => new sfValidatorPass(array('required' => false)),
      'mobile'                                      => new sfValidatorPass(array('required' => false)),
      'recovery_key'                                => new sfValidatorPass(array('required' => false)),
      'activation_key'                              => new sfValidatorPass(array('required' => false)),
      'registration_notification'                   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'agreement_notification'                      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'new_agreement_notification'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'final_agreement_notification'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'agreement_report_notification'               => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'new_agreement_report_notification'           => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'final_agreement_report_notification'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'agreement_concept_notification'              => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'new_agreement_concept_notification'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'final_agreement_concept_notification'        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'agreement_concept_report_notification'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'new_agreement_concept_report_notification'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'final_agreement_concept_report_notification' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'dealer_discussion_notification'              => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'model_discussion_notification'               => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'active'                                      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('user_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'User';
  }

  public function getFields()
  {
    return array(
      'id'                                          => 'Number',
      'group_id'                                    => 'ForeignKey',
      'email'                                       => 'Text',
      'password'                                    => 'Text',
      'name'                                        => 'Text',
      'surname'                                     => 'Text',
      'patronymic'                                  => 'Text',
      'company_type'                                => 'Enum',
      'company_name'                                => 'Text',
      'post'                                        => 'Text',
      'phone'                                       => 'Text',
      'mobile'                                      => 'Text',
      'recovery_key'                                => 'Text',
      'activation_key'                              => 'Text',
      'registration_notification'                   => 'Boolean',
      'agreement_notification'                      => 'Boolean',
      'new_agreement_notification'                  => 'Boolean',
      'final_agreement_notification'                => 'Boolean',
      'agreement_report_notification'               => 'Boolean',
      'new_agreement_report_notification'           => 'Boolean',
      'final_agreement_report_notification'         => 'Boolean',
      'agreement_concept_notification'              => 'Boolean',
      'new_agreement_concept_notification'          => 'Boolean',
      'final_agreement_concept_notification'        => 'Boolean',
      'agreement_concept_report_notification'       => 'Boolean',
      'new_agreement_concept_report_notification'   => 'Boolean',
      'final_agreement_concept_report_notification' => 'Boolean',
      'dealer_discussion_notification'              => 'Boolean',
      'model_discussion_notification'               => 'Boolean',
      'active'                                      => 'Boolean',
      'created_at'                                  => 'Date',
      'updated_at'                                  => 'Date',
    );
  }
}
