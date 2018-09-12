<?php

/**
 * UpdateActivityStatus filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUpdateActivityStatusFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => true)),
      'dealer_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'activity_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Activity'), 'column' => 'id')),
      'dealer_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Dealer'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('update_activity_status_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UpdateActivityStatus';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'activity_id' => 'ForeignKey',
      'dealer_id'   => 'ForeignKey',
    );
  }
}
