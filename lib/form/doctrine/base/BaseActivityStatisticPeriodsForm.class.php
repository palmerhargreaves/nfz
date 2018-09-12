<?php

/**
 * ActivityStatisticPeriods form base class.
 *
 * @method ActivityStatisticPeriods getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseActivityStatisticPeriodsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'quarters'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Quarters')),
      //'quarters'        => new sfWidgetFormChoice(array('choices' => array('1' => '1 Квартал', '2' => '2 Квартал', '3' => '3 Квартал', '4' => '4 Квартал'))),
      'year'        => new sfWidgetFormChoice(array('choices' => array(), 'multiple' => true)),
      'activity_id' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'quarters'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Quarters', 'required' => true)),
      //'quarters'      => new sfValidatorChoice(array('choices' => array(0 => '1', 1 => '2', 2 => '3', 3 => '4'))),
      'year'     => new sfValidatorChoice(array('choices' => '', 'required' => true)),
      'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'))),
    ));

    $this->widgetSchema->setNameFormat('activity_statistic_periods[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ActivityStatisticPeriods';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['quarters'])) {
      $this->setDefault('quarters', $this->object->getQuartersList());
    }

    if(isset($this->widgetSchema['year'])) {
      $this->setDefault('year', $this->object->getCorrectYear());
    }

  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->saveStatQuartals($con);
  }

  public function saveStatQuartals($con = null)
  {
    if (!$this->isValid()) {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['quarters'])) {
      // somebody has unset this widget
      return;
    }

    if (null === $con) {
      $con = $this->getConnection();
    }
    
    $id = $this->object->getId();
    $values = $this->getValue('quarters');
  
    if(!empty($id)) {
      $this->object->updateQuartersList($this->getValue('activity_id'), $this->getValue('year'), $this->getValue('quarters'));
    }
    
  }
}
