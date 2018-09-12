<?php

/**
 * News filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseNewsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'announcement' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'img_small'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'img_big'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'text'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'date_of_add'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'user_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'status'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_important' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'name'         => new sfValidatorPass(array('required' => false)),
      'announcement' => new sfValidatorPass(array('required' => false)),
      'img_small'    => new sfValidatorPass(array('required' => false)),
      'img_big'      => new sfValidatorPass(array('required' => false)),
      'text'         => new sfValidatorPass(array('required' => false)),
      'date_of_add'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'user_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'status'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_important' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('news_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'News';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'name'         => 'Text',
      'announcement' => 'Text',
      'img_small'    => 'Text',
      'img_big'      => 'Text',
      'text'         => 'Text',
      'date_of_add'  => 'Date',
      'user_id'      => 'ForeignKey',
      'status'       => 'Number',
      'is_important' => 'Number',
      'created_at'   => 'Date',
      'updated_at'   => 'Date',
    );
  }
}
