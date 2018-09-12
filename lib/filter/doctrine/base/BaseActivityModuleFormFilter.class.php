<?php

/**
 * ActivityModule filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseActivityModuleFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'identifier'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sort'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'activities_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Activity')),
    ));

    $this->setValidators(array(
      'name'            => new sfValidatorPass(array('required' => false)),
      'identifier'      => new sfValidatorPass(array('required' => false)),
      'sort'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'activities_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Activity', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('activity_module_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addActivitiesListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('AcivityModuleActivity.activity_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'ActivityModule';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'name'            => 'Text',
      'identifier'      => 'Text',
      'sort'            => 'Number',
      'activities_list' => 'ManyKey',
    );
  }
}
