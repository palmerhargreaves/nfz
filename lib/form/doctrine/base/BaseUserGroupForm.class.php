<?php

/**
 * UserGroup form base class.
 *
 * @method UserGroup getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUserGroupForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'name'       => new sfWidgetFormInputText(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
      'roles_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Role')),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'       => new sfValidatorString(array('max_length' => 255)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
      'roles_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Role', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('user_group[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UserGroup';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['roles_list']))
    {
      $this->setDefault('roles_list', $this->object->Roles->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveRolesList($con);

    parent::doSave($con);
  }

  public function saveRolesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['roles_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Roles->getPrimaryKeys();
    $values = $this->getValue('roles_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Roles', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Roles', array_values($link));
    }
  }

}
