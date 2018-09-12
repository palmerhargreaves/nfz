<?php

/**
 * User form base class.
 *
 * @method User getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUserForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => true)),
            'email' => new sfWidgetFormInputText(),
            'password' => new sfWidgetFormInputText(),
            'name' => new sfWidgetFormInputText(),
            'surname' => new sfWidgetFormInputText(),
            'patronymic' => new sfWidgetFormInputText(),
            'company_type' => new sfWidgetFormChoice(array('choices' => array('dealer' => 'dealer', 'importer' => 'importer', 'regional_manager' => 'regional_manager', 'other' => 'other'))),
            'company_name' => new sfWidgetFormInputText(),
            'post' => new sfWidgetFormInputText(),
            'phone' => new sfWidgetFormInputText(),
            'mobile' => new sfWidgetFormInputText(),
            'recovery_key' => new sfWidgetFormInputText(),
            'activation_key' => new sfWidgetFormInputText(),
            'registration_notification' => new sfWidgetFormInputCheckbox(),
            'agreement_notification' => new sfWidgetFormInputCheckbox(),
            'new_agreement_notification' => new sfWidgetFormInputCheckbox(),
            'final_agreement_notification' => new sfWidgetFormInputCheckbox(),
            'agreement_report_notification' => new sfWidgetFormInputCheckbox(),
            'new_agreement_report_notification' => new sfWidgetFormInputCheckbox(),
            'final_agreement_report_notification' => new sfWidgetFormInputCheckbox(),
            'agreement_concept_notification' => new sfWidgetFormInputCheckbox(),
            'new_agreement_concept_notification' => new sfWidgetFormInputCheckbox(),
            'final_agreement_concept_notification' => new sfWidgetFormInputCheckbox(),
            'agreement_concept_report_notification' => new sfWidgetFormInputCheckbox(),
            'new_agreement_concept_report_notification' => new sfWidgetFormInputCheckbox(),
            'final_agreement_concept_report_notification' => new sfWidgetFormInputCheckbox(),
            'allow_to_get_dealers_messages' => new sfWidgetFormInputCheckbox(),
            'dealer_discussion_notification' => new sfWidgetFormInputCheckbox(),
            'model_discussion_notification' => new sfWidgetFormInputCheckbox(),
            'active' => new sfWidgetFormInputCheckbox(),
            'allow_receive_mails' => new sfWidgetFormInputCheckbox(),
            'dealers_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Dealer')),
            'natural_person_id' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'NaturalPerson')),
            'created_at' => new sfWidgetFormDateTime(),
            'updated_at' => new sfWidgetFormDateTime(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'required' => false)),
            'email' => new sfValidatorString(array('max_length' => 255)),
            'password' => new sfValidatorString(array('max_length' => 255)),
            'name' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'surname' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'patronymic' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'company_type' => new sfValidatorChoice(array('choices' => array(0 => 'dealer', 1 => 'importer', 2 => 'regional_manager', 3 => 'other'))),
            'company_name' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'post' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'phone' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'mobile' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'recovery_key' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'activation_key' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'registration_notification' => new sfValidatorBoolean(array('required' => false)),
            'agreement_notification' => new sfValidatorBoolean(array('required' => false)),
            'new_agreement_notification' => new sfValidatorBoolean(array('required' => false)),
            'final_agreement_notification' => new sfValidatorBoolean(array('required' => false)),
            'agreement_report_notification' => new sfValidatorBoolean(array('required' => false)),
            'new_agreement_report_notification' => new sfValidatorBoolean(array('required' => false)),
            'final_agreement_report_notification' => new sfValidatorBoolean(array('required' => false)),
            'agreement_concept_notification' => new sfValidatorBoolean(array('required' => false)),
            'new_agreement_concept_notification' => new sfValidatorBoolean(array('required' => false)),
            'final_agreement_concept_notification' => new sfValidatorBoolean(array('required' => false)),
            'agreement_concept_report_notification' => new sfValidatorBoolean(array('required' => false)),
            'new_agreement_concept_report_notification' => new sfValidatorBoolean(array('required' => false)),
            'final_agreement_concept_report_notification' => new sfValidatorBoolean(array('required' => false)),
            'allow_to_get_dealers_messages' => new sfValidatorBoolean(array('required' => false)),
            'dealer_discussion_notification' => new sfValidatorBoolean(array('required' => false)),
            'model_discussion_notification' => new sfValidatorBoolean(array('required' => false)),
            'active' => new sfValidatorBoolean(array('required' => false)),
            'allow_receive_mails' => new sfValidatorBoolean(array('required' => false)),
            'dealers_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Dealer', 'required' => false)),
            'natural_person_id' => new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'NaturalPerson', 'required' => false)),
            'created_at' => new sfValidatorDateTime(),
            'updated_at' => new sfValidatorDateTime(),
        ));

        $this->validatorSchema->setPostValidator(
            new sfValidatorDoctrineUnique(array('model' => 'User', 'column' => array('email')))
        );

        $this->widgetSchema->setNameFormat('user[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'User';
    }


    public function updateDefaultsFromObject()
    {
        parent::updateDefaultsFromObject();

        if (isset($this->widgetSchema['dealers_list'])) {
            //$this->setDefault('dealers_list', $this->object->Dealers->getPrimaryKeys());
            $this->setDefault('dealers_list', $this->object->getDealersList());
        }
    }

    protected function doSave($con = null)
    {
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
            $existing = $this->object->AllowedDealers->getPrimaryKeys();
            $link = array_diff($values, $existing);
            if (count($link)) {
                $this->object->link('AllowedDealers', array_values($link));
            }
        }
    }
}
