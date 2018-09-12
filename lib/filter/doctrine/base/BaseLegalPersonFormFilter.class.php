<?php

/**
 * LegalPerson filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLegalPersonFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'legal_address'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'INN'                   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'KPP'                   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'OKPO'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'transactional_account' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'correspondent_account' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'BIK'                   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'bank_name'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'name'                  => new sfValidatorPass(array('required' => false)),
      'legal_address'         => new sfValidatorPass(array('required' => false)),
      'INN'                   => new sfValidatorPass(array('required' => false)),
      'KPP'                   => new sfValidatorPass(array('required' => false)),
      'OKPO'                  => new sfValidatorPass(array('required' => false)),
      'transactional_account' => new sfValidatorPass(array('required' => false)),
      'correspondent_account' => new sfValidatorPass(array('required' => false)),
      'BIK'                   => new sfValidatorPass(array('required' => false)),
      'bank_name'             => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('legal_person_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LegalPerson';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'name'                  => 'Text',
      'legal_address'         => 'Text',
      'INN'                   => 'Text',
      'KPP'                   => 'Text',
      'OKPO'                  => 'Text',
      'transactional_account' => 'Text',
      'correspondent_account' => 'Text',
      'BIK'                   => 'Text',
      'bank_name'             => 'Text',
    );
  }
}
