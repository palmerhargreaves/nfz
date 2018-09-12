<?php

/**
 * UpdateActivityStatus form base class.
 *
 * @method UpdateActivityStatus getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUpdateActivityStatusForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => false)),
      'dealer_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'))),
      'dealer_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'))),
    ));

    $this->widgetSchema->setNameFormat('update_activity_status[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UpdateActivityStatus';
  }

}
