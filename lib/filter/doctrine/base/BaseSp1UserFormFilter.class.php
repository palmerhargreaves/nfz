<?php

/**
 * Sp1User filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSp1UserFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'dealer_number' => new sfWidgetFormFilterInput(),
      'company'       => new sfWidgetFormFilterInput(),
      'post'          => new sfWidgetFormFilterInput(),
      'family'        => new sfWidgetFormFilterInput(),
      'name'          => new sfWidgetFormFilterInput(),
      'email'         => new sfWidgetFormFilterInput(),
      'phone'         => new sfWidgetFormFilterInput(),
      'mobile_phone'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'dealer_number' => new sfValidatorPass(array('required' => false)),
      'company'       => new sfValidatorPass(array('required' => false)),
      'post'          => new sfValidatorPass(array('required' => false)),
      'family'        => new sfValidatorPass(array('required' => false)),
      'name'          => new sfValidatorPass(array('required' => false)),
      'email'         => new sfValidatorPass(array('required' => false)),
      'phone'         => new sfValidatorPass(array('required' => false)),
      'mobile_phone'  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sp1_user_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Sp1User';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'dealer_number' => 'Text',
      'company'       => 'Text',
      'post'          => 'Text',
      'family'        => 'Text',
      'name'          => 'Text',
      'email'         => 'Text',
      'phone'         => 'Text',
      'mobile_phone'  => 'Text',
    );
  }
}
