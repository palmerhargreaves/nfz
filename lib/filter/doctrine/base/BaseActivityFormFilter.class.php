<?php

/**
 * Activity filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseActivityFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'start_date'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'end_date'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'custom_date'   => new sfWidgetFormFilterInput(),
      'description'   => new sfWidgetFormFilterInput(),
      'brief'         => new sfWidgetFormFilterInput(),
      'materials_url' => new sfWidgetFormFilterInput(),
      'finished'      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'importance'    => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'has_concept'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'hide'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'sort'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'modules_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ActivityModule')),
    ));

    $this->setValidators(array(
      'name'          => new sfValidatorPass(array('required' => false)),
      'start_date'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'end_date'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'custom_date'   => new sfValidatorPass(array('required' => false)),
      'description'   => new sfValidatorPass(array('required' => false)),
      'brief'         => new sfValidatorPass(array('required' => false)),
      'materials_url' => new sfValidatorPass(array('required' => false)),
      'finished'      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'importance'    => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'has_concept'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'hide'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'sort'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'modules_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ActivityModule', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('activity_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addModulesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.AcivityModuleActivity AcivityModuleActivity')
      ->andWhereIn('AcivityModuleActivity.module_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Activity';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'name'          => 'Text',
      'start_date'    => 'Date',
      'end_date'      => 'Date',
      'custom_date'   => 'Text',
      'description'   => 'Text',
      'brief'         => 'Text',
      'materials_url' => 'Text',
      'finished'      => 'Boolean',
      'importance'    => 'Boolean',
      'has_concept'   => 'Boolean',
      'hide'          => 'Boolean',
      'sort'          => 'Number',
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
      'modules_list'  => 'ManyKey',
    );
  }
}
