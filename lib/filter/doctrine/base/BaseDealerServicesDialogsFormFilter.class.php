<?php

/**
 * DealerServicesDialogs filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDealerServicesDialogsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'header'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'header'        => new sfValidatorPass(array('required' => false)),
      'activity_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Activity'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('dealer_services_dialogs[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DealerServicesDialogs';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'header'        => 'Text',
      'activity_id' => 'ForeignKey',
    );
  }
}
