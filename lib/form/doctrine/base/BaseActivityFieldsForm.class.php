<?php

/**
 * ActivityInfo form base class.
 *
 * @method ActivityInfo getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseActivityFieldsForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => true)),
            'parent_header_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityVideoRecordsStatisticsHeaders'), 'add_empty' => true)),
            'name' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextarea(),
            'group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityVideoRecordsStatisticsHeadersGroups'), 'add_empty' => true)),
            'type' => new sfWidgetFormChoice(array('choices' => array('string' => 'Строка', 'date' => 'Дата', 'number' => 'Число', 'file' => 'Файл'))),
            'content' => new sfWidgetFormChoice(array('choices' => array('price' => 'Сумма', 'counts' => 'Количество', 'other' => 'Другое'))),
            'req' => new sfWidgetFormInputCheckbox(),
            'status' => new sfWidgetFormInputCheckbox(),
            'created_at' => new sfWidgetFormDateTime(),
            'updated_at' => new sfWidgetFormDateTime(),

        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'required' => false)),
            'parent_header_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityVideoRecordsStatisticsHeaders'))),
            'name' => new sfValidatorString(array('max_length' => 255)),
            'description' => new sfValidatorString(array('required' => false)),
            'group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityVideoRecordsStatisticsHeadersGroups'), 'required' => false)),
            'type' => new sfValidatorChoice(array('choices' => array(0 => 'string', 1 => 'date', 2 => 'number', 3 => 'file'))),
            'content' => new sfValidatorChoice(array('choices' => array(0 => 'price', 1 => 'counts', 2 => 'other'))),
            'req' => new sfValidatorBoolean(array('required' => false)),
            'status' => new sfValidatorBoolean(array('required' => false)),
            'created_at' => new sfValidatorDateTime(),
            'updated_at' => new sfValidatorDateTime(),
        ));

        $this->widgetSchema->setNameFormat('activity_fields[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ActivityFields';
    }

}
