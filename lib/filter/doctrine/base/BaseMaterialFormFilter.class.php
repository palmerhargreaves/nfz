<?php

/**
 * Material filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMaterialFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'category_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Category'), 'add_empty' => true)),
            'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => true)),
            'categories_list' =>  new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'Category')),
            'activities_list' =>  new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'Activity')),
            'name' => new sfWidgetFormFilterInput(array('with_empty' => false)),
            'file_preview' => new sfWidgetFormFilterInput(),
            'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
            'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
        ));

        $this->setValidators(array(
            'category_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Category'), 'column' => 'id')),
            'activity_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Activity'), 'column' => 'id')),
            'categories_list' => new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'MaterialCategory', 'required' => false)),
            'activities_list' => new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'Activity', 'required' => false)),
            'name' => new sfValidatorPass(array('required' => false)),
            'file_preview' => new sfValidatorPass(array('required' => false)),
            'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
            'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
        ));

        $this->widgetSchema->setNameFormat('material_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Material';
    }

    public function getFields()
    {
        return array(
            'id' => 'Number',
            'category_id' => 'ForeignKey',
            //'activity_id' => 'ForeignKey',
            'activities_list' => 'activities_list',
            'name' => 'Text',
            'file_preview' => 'Text',
            'created_at' => 'Date',
            'updated_at' => 'Date',
        );
    }
}
