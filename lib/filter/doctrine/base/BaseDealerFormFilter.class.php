<?php

/**
 * Dealer filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDealerFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'number'              => new sfWidgetFormFilterInput(),
      'name'                => new sfWidgetFormFilterInput(),
      'address'             => new sfWidgetFormFilterInput(),
      'phone'               => new sfWidgetFormFilterInput(),
      'site'                => new sfWidgetFormFilterInput(),
      'email'               => new sfWidgetFormFilterInput(),
      'longitude'           => new sfWidgetFormFilterInput(),
      'latitude'            => new sfWidgetFormFilterInput(),
      'city_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('City'), 'add_empty' => true)),
      'regional_manager_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('RegionalManager'), 'add_empty' => true)),
      'company_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('LegalPerson'), 'add_empty' => true)),
      'importer_id'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'number'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'name'                => new sfValidatorPass(array('required' => false)),
      'address'             => new sfValidatorPass(array('required' => false)),
      'phone'               => new sfValidatorPass(array('required' => false)),
      'site'                => new sfValidatorPass(array('required' => false)),
      'email'               => new sfValidatorPass(array('required' => false)),
      'longitude'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'latitude'            => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'city_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('City'), 'column' => 'id')),
      'regional_manager_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('RegionalManager'), 'column' => 'id')),
      'company_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('LegalPerson'), 'column' => 'id')),
      'importer_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('dealer_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Dealer';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'number'              => 'Number',
      'name'                => 'Text',
      'address'             => 'Text',
      'phone'               => 'Text',
      'site'                => 'Text',
      'email'               => 'Text',
      'longitude'           => 'Number',
      'latitude'            => 'Number',
      'city_id'             => 'ForeignKey',
      'regional_manager_id' => 'ForeignKey',
      'company_id'          => 'ForeignKey',
      'importer_id'         => 'Number',
    );
  }
}
