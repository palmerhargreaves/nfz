<?php

/**
 * AcivityModuleActivity form base class.
 *
 * @method AcivityModuleActivity getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAcivityModuleActivityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'activity_id' => new sfWidgetFormInputHidden(),
      'module_id'   => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'activity_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('activity_id')), 'empty_value' => $this->getObject()->get('activity_id'), 'required' => false)),
      'module_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('module_id')), 'empty_value' => $this->getObject()->get('module_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('acivity_module_activity[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AcivityModuleActivity';
  }

}
