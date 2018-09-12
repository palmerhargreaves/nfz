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
abstract class BaseActivityVideoRecordsStatisticsForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => false)),
            'header' => new sfWidgetFormInputText(),
            'status' => new sfWidgetFormInputCheckbox(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'))),
            'header' => new sfValidatorString(array('max_length' => 255)),
            'status' => new sfValidatorBoolean(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('activity_video_records_statistics[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ActivityVideoRecordsStatistics';
    }

}
