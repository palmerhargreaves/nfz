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
abstract class BaseActivityVideoRecordsStatisticsHeadersFieldsForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'parent_header_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityVideoRecordsStatisticsHeaders'), 'add_empty' => false)),
            'name' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextarea(),
            'group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityVideoRecordsStatisticsHeadersGroups'), 'add_empty' => true)),
            'type' => new sfWidgetFormChoice(array('choices' => array('string' => 'Строка', 'date' => 'Дата', 'number' => 'Число'))),
            'content' => new sfWidgetFormChoice(array('choices' => array('price' => 'Сумма', 'counts' => 'Количество', 'other' => 'Другое'))),
            'required' => new sfWidgetFormInputCheckbox(),
            'status' => new sfWidgetFormInputCheckbox(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'parent_header_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityVideoRecordsStatisticsHeaders'))),
            'name' => new sfValidatorString(array('max_length' => 255)),
            'description' => new sfValidatorString(array('required' => false)),
            'group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityVideoRecordsStatisticsHeadersGroups'), 'required' => false)),
            'type' => new sfValidatorChoice(array('choices' => array(0 => 'string', 1 => 'date', 2 => 'number'))),
            'content' => new sfValidatorChoice(array('choices' => array(0 => 'price', 1 => 'counts', 2 => 'other'))),
            'required' => new sfValidatorBoolean(array('required' => false)),
            'status' => new sfValidatorBoolean(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('activity_video_records_statistics_headers_fields[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ActivityFields';
    }

}
