<?php

/**
 * NaturalPerson filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseNaturalPersonFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'firstname'   => new sfWidgetFormFilterInput(),
      'surname'     => new sfWidgetFormFilterInput(),
      'patronym'    => new sfWidgetFormFilterInput(),
      'team_id'     => new sfWidgetFormFilterInput(),
      'dealer_id'   => new sfWidgetFormFilterInput(),
      'importer_id' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'firstname'   => new sfValidatorPass(array('required' => false)),
      'surname'     => new sfValidatorPass(array('required' => false)),
      'patronym'    => new sfValidatorPass(array('required' => false)),
      'team_id'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'dealer_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'importer_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('natural_person_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'NaturalPerson';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'firstname'   => 'Text',
      'surname'     => 'Text',
      'patronym'    => 'Text',
      'team_id'     => 'Number',
      'dealer_id'   => 'Number',
      'importer_id' => 'Number',
    );
  }
}
