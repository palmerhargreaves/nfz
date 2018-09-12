<?php

/**
 * Material form base class.
 *
 * @method Material getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMaterialForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'category_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Category'), 'add_empty' => false)),
            'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => true)),
            'activities_list' =>  new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Activity')),
            'name' => new sfWidgetFormInputText(),
            'file_preview' => new sfWidgetFormInputText(),
            'editor_link' => new sfWidgetFormInputText(),
            'material_status' => new sfWidgetFormInputCheckbox(),
            'created_at' => new sfWidgetFormDateTime(),
            'updated_at' => new sfWidgetFormDateTime(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'category_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Category'))),
            'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'required' => false)),
            'activities_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Activity', 'required' => true)),
            'name' => new sfValidatorString(array('max_length' => 255)),
            'file_preview' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'editor_link' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'material_status' => new sfValidatorBoolean(array('required' => false)),
            'created_at' => new sfValidatorDateTime(),
            'updated_at' => new sfValidatorDateTime(),
        ));

        $this->widgetSchema->setNameFormat('material[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Material';
    }

    public function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();

        if (isset($this->widgetSchema['activities_list'])) {
            $this->setDefault('activities_list', $this->object->getActivitiesList());
        }
    }

    protected function doSave($con = null)
    {
        parent::doSave($con);

        $this->saveActivitiesList($con);
    }

    private function saveActivitiesList($con = null) {
        if (!$this->isValid()) {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['activities_list'])) {
            return;
        }

        if (null === $con) {
            $con = $this->getConnection();
        }

        $id = $this->object->getId();
        $values = $this->getValue('activities_list');

        if (!empty($id)) {
            $this->object->updateActivitiesList($this->getValue('activities_list'));
        } else {
            $existing = $this->object->Activities->getPrimaryKeys();
            $link = array_diff($values, $existing);
            if (count($link)) {
                $this->object->link('Activities', array_values($link));
            }
        }
    }
}
