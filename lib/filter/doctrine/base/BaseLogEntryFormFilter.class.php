<?php

/**
 * LogEntry filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLogEntryFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'login'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'title'           => new sfWidgetFormFilterInput(),
      'description'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'icon'            => new sfWidgetFormFilterInput(),
      'object_id'       => new sfWidgetFormFilterInput(),
      'object_type'     => new sfWidgetFormFilterInput(),
      'module_id'       => new sfWidgetFormFilterInput(),
      'action'          => new sfWidgetFormFilterInput(),
      'importance'      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'dealer_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => true)),
      'message_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Message'), 'add_empty' => true)),
      'private_user_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PrivateUser'), 'add_empty' => true)),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'user_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'login'           => new sfValidatorPass(array('required' => false)),
      'title'           => new sfValidatorPass(array('required' => false)),
      'description'     => new sfValidatorPass(array('required' => false)),
      'icon'            => new sfValidatorPass(array('required' => false)),
      'object_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'object_type'     => new sfValidatorPass(array('required' => false)),
      'module_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'action'          => new sfValidatorPass(array('required' => false)),
      'importance'      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'dealer_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Dealer'), 'column' => 'id')),
      'message_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Message'), 'column' => 'id')),
      'private_user_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('PrivateUser'), 'column' => 'id')),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('log_entry_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogEntry';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'user_id'         => 'ForeignKey',
      'login'           => 'Text',
      'title'           => 'Text',
      'description'     => 'Text',
      'icon'            => 'Text',
      'object_id'       => 'Number',
      'object_type'     => 'Text',
      'module_id'       => 'Number',
      'action'          => 'Text',
      'importance'      => 'Boolean',
      'dealer_id'       => 'ForeignKey',
      'message_id'      => 'ForeignKey',
      'private_user_id' => 'ForeignKey',
      'created_at'      => 'Date',
    );
  }
}
