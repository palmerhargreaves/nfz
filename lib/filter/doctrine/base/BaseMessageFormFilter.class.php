<?php

/**
 * Message filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMessageFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'discussion_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Discussion'), 'add_empty' => true)),
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'private_user_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PrivateUser'), 'add_empty' => true)),
      'user_name'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'text'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'system'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'discussion_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Discussion'), 'column' => 'id')),
      'user_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'private_user_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('PrivateUser'), 'column' => 'id')),
      'user_name'       => new sfValidatorPass(array('required' => false)),
      'text'            => new sfValidatorPass(array('required' => false)),
      'system'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('message_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Message';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'discussion_id'   => 'ForeignKey',
      'user_id'         => 'ForeignKey',
      'private_user_id' => 'ForeignKey',
      'user_name'       => 'Text',
      'text'            => 'Text',
      'system'          => 'Boolean',
      'created_at'      => 'Date',
    );
  }
}
