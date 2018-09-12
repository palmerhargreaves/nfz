<?php

/**
 * Role filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseRoleFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'role'        => new sfWidgetFormChoice(array('choices' => array('' => '', 'admin' => 'admin', 'importer' => 'importer', 'dealer' => 'dealer', 'manager' => 'manager', 'specialist' => 'specialist'))),
      'groups_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'UserGroup')),
    ));

    $this->setValidators(array(
      'name'        => new sfValidatorPass(array('required' => false)),
      'role'        => new sfValidatorChoice(array('required' => false, 'choices' => array('admin' => 'admin', 'importer' => 'importer', 'dealer' => 'dealer', 'manager' => 'manager', 'specialist' => 'specialist'))),
      'groups_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'UserGroup', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('role_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addGroupsListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.GroupRole GroupRole')
      ->andWhereIn('GroupRole.group_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Role';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'name'        => 'Text',
      'role'        => 'Enum',
      'groups_list' => 'ManyKey',
    );
  }
}
