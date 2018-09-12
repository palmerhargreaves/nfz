<?php

/**
 * VwUser filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseVwUserFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'email'     => new sfWidgetFormFilterInput(),
      'person_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Person'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'email'     => new sfValidatorPass(array('required' => false)),
      'person_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Person'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('vw_user_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'VwUser';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'email'     => 'Text',
      'person_id' => 'ForeignKey',
    );
  }
}
