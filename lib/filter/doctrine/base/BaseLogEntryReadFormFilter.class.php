<?php

/**
 * LogEntryRead filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLogEntryReadFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'entry_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Entry'), 'add_empty' => true)),
      'user_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'entry_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Entry'), 'column' => 'id')),
      'user_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('log_entry_read_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogEntryRead';
  }

  public function getFields()
  {
    return array(
      'id'       => 'Number',
      'entry_id' => 'ForeignKey',
      'user_id'  => 'ForeignKey',
    );
  }
}
