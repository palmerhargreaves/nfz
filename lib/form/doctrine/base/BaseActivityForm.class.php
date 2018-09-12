<?php

/**
 * Activity form base class.
 *
 * @method Activity getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseActivityForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText(),
            'start_date' => new sfWidgetFormDate(),
            'end_date' => new sfWidgetFormDate(),
            'custom_date' => new sfWidgetFormTextarea(),
            'description' => new sfWidgetFormTextarea(),
            'brief' => new sfWidgetFormTextarea(),
            'materials_url' => new sfWidgetFormInputText(),
            'finished' => new sfWidgetFormInputCheckbox(),
            'importance' => new sfWidgetFormInputCheckbox(),
            'has_concept' => new sfWidgetFormInputCheckbox(),
            'many_concepts' => new sfWidgetFormInputCheckbox(),
            'is_concept_complete' => new sfWidgetFormInputCheckbox(),
            'hide' => new sfWidgetFormInputCheckbox(),
            'select_activity' => new sfWidgetFormInputCheckbox(),
            'sort' => new sfWidgetFormInputText(),
            'created_at' => new sfWidgetFormDateTime(),
            'updated_at' => new sfWidgetFormDateTime(),
            'modules_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ActivityModule')),
            'dealers_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Dealer')),
            'is_limit_run' => new sfWidgetFormInputCheckbox(),
            'is_own' => new sfWidgetFormInputCheckbox(),
            'allow_extended_statistic' => new sfWidgetFormInputCheckbox(),
            'allow_certificate' => new sfWidgetFormInputCheckbox(),
            'allow_to_all_dealers' => new sfWidgetFormInputCheckbox(),
            'stats_description' => new sfWidgetFormTextarea(),
            'mandatory_activity' => new sfWidgetFormInputCheckbox(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'name' => new sfValidatorString(array('max_length' => 255)),
            'start_date' => new sfValidatorDate(array('required' => true)),
            'end_date' => new sfValidatorDate(array('required' => true)),
            'custom_date' => new sfValidatorString(array('required' => false)),
            'description' => new sfValidatorString(array('required' => false)),
            'brief' => new sfValidatorString(array('required' => false)),
            'materials_url' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'finished' => new sfValidatorBoolean(array('required' => false)),
            'importance' => new sfValidatorBoolean(array('required' => false)),
            'has_concept' => new sfValidatorBoolean(array('required' => false)),
            'many_concepts' => new sfValidatorBoolean(array('required' => false)),
            'is_concept_complete' => new sfValidatorBoolean(array('required' => false)),
            'hide' => new sfValidatorBoolean(array('required' => false)),
            'select_activity' => new sfValidatorBoolean(array('required' => false)),
            'sort' => new sfValidatorInteger(array('required' => false)),
            'created_at' => new sfValidatorDateTime(),
            'updated_at' => new sfValidatorDateTime(),
            'modules_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ActivityModule', 'required' => false)),
            'dealers_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Dealer', 'required' => false)),
            'is_limit_run' => new sfValidatorBoolean(array('required' => false)),
            'is_own' => new sfValidatorBoolean(array('required' => false)),
            'allow_extended_statistic' => new sfValidatorBoolean(array('required' => false)),
            'allow_certificate' => new sfValidatorBoolean(array('required' => false)),
            'allow_to_all_dealers' => new sfValidatorBoolean(array('required' => false)),
            'stats_description' => new sfValidatorString(array('required' => false)),
            'mandatory_activity' => new sfValidatorBoolean(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('activity[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function isValid()
    {
        foreach ($this->validatorSchema->getFields() as $val) {
            //if($val->isValid()) {}
        }


        return parent::isValid();
    }

    public function getModelName()
    {
        return 'Activity';
    }

    public function updateDefaultsFromObject()
    {
        parent::updateDefaultsFromObject();

        if (isset($this->widgetSchema['modules_list'])) {
            $this->setDefault('modules_list', $this->object->Modules->getPrimaryKeys());
        }

        if (isset($this->widgetSchema['dealers_list'])) {
            $this->setDefault('dealers_list', $this->object->getDealersList());
        }

    }

    protected function doSave($con = null)
    {

        $this->saveModulesList($con);
        $this->saveDealersList($con);

        parent::doSave($con);
    }

    public function saveDealersList($con = null)
    {
        if (!$this->isValid()) {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['dealers_list'])) {
            // somebody has unset this widget
            return;
        }

        if (null === $con) {
            $con = $this->getConnection();
        }

        $id = $this->object->getId();
        $values = $this->getValue('dealers_list');

        if (!empty($id)) {
            $this->object->updateDealersList($this->getValue('dealers_list'));
        } else {
            $existing = $this->object->Dealers->getPrimaryKeys();
            $link = array_diff($values, $existing);
            if (count($link)) {
                $this->object->link('Dealers', array_values($link));
            }
        }
    }

    public function saveModulesList($con = null)
    {
        if (!$this->isValid()) {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['modules_list'])) {
            // somebody has unset this widget
            return;
        }

        if (null === $con) {
            $con = $this->getConnection();
        }

        $existing = $this->object->Modules->getPrimaryKeys();
        $values = $this->getValue('modules_list');
        if (!is_array($values)) {
            $values = array();
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink)) {
            $this->object->unlink('Modules', array_values($unlink));
        }

        $link = array_diff($values, $existing);
        if (count($link)) {
            $this->object->link('Modules', array_values($link));
        }
    }

}
