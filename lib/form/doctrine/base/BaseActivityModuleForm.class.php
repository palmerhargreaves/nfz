<?php

/**
 * ActivityModule form base class.
 *
 * @method ActivityModule getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseActivityModuleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'name'            => new sfWidgetFormInputText(),
      'identifier'      => new sfWidgetFormInputText(),
      'sort'            => new sfWidgetFormInputText(),
      'activities_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Activity')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'            => new sfValidatorString(array('max_length' => 255)),
      'identifier'      => new sfValidatorString(array('max_length' => 255)),
      'sort'            => new sfValidatorInteger(array('required' => false)),
      'activities_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Activity', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('activity_module[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ActivityModule';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['activities_list']))
    {
      $this->setDefault('activities_list', $this->object->Activities->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveActivitiesList($con);

    parent::doSave($con);
  }

  public function saveActivitiesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['activities_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Activities->getPrimaryKeys();
    $values = $this->getValue('activities_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Activities', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Activities', array_values($link));
    }
  }

}
