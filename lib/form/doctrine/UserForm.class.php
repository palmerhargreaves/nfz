<?php

/**
 * User form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UserForm extends BaseUserForm
{
    public function configure()
    {
        unset($this['created_at'], $this['updated_at'], $this['password'], $this['recovery_key'], $this['activation_key']);

        $this->widgetSchema['new_password'] = new sfWidgetFormInputPassword();
        $this->validatorSchema['new_password'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $this->widgetSchema['new_password_confirmation'] = new sfWidgetFormInputPassword();
        $this->validatorSchema['new_password_confirmation'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $this->widgetSchema['company_type'] = new sfWidgetFormChoice(array(
            'choices' => array(
                'dealer' => 'Дилер', 'importer' => 'Импортер', 'regional_manager' => 'Региональный менеджер', 'other' => 'Другое'
            )
        ));
        $this->validatorSchema['company_type'] = new sfValidatorChoice(array(
            'choices' => array(
                'dealer', 'importer', 'regional_manager', 'other'
            )
        ));

        $this->widgetSchema->setLabels(array(
            'new_password' => 'Введите пароль',
            'new_password_confirmation' => 'Повторите пароль',
        ));

        $this->widgetSchema->setPositions(array(
            'id',
            'group_id',
            'email',
            'surname',
            'name',
            'patronymic',
            'company_type',
            'company_name',
            'post',
            'phone',
            'mobile',
            'new_password',
            'new_password_confirmation',
            'natural_person_id',
            'dealers_list',
            'registration_notification',
            'new_agreement_notification',
            'agreement_notification',
            'final_agreement_notification',
            'new_agreement_report_notification',
            'agreement_report_notification',
            'final_agreement_report_notification',
            'new_agreement_concept_notification',
            'agreement_concept_notification',
            'final_agreement_concept_report_notification',
            'allow_to_get_dealers_messages',
            'new_agreement_concept_report_notification',
            'agreement_concept_report_notification',
            'final_agreement_concept_notification',
            'dealer_discussion_notification',
            'model_discussion_notification',
            'active',
            'allow_receive_mails'

        ));

        $this->widgetSchema['dealers_list'] = new sfWidgetFormDoctrineChoice(array('model' => 'Dealer', 'query' => Doctrine::getTable('Dealer')->getDealersList(), 'add_empty' => false, 'multiple' => true, 'method' => 'getNameAndNumber'));
        $this->validatorsSchema['dealers_list'] = new sfValidatorDoctrineChoice(array('model' => 'Dealer', 'multiple' => true, 'required' => false));

        $this->widgetSchema['natural_person_id'] = new sfWidgetFormDoctrineChoice(array('model' => 'NaturalPerson', 'query' => Doctrine::getTable('NaturalPerson')->getActivePersonsList(), 'add_empty' => true, 'method' => 'getPersonFullName'));
        $this->validatorSchema['natural_person_id'] = new sfValidatorDoctrineChoice(array('model' => 'NaturalPerson', 'multiple' => false, 'required' => false));

        $this->validatorSchema->setPostValidator(
            new sfValidatorAnd(array(
                new sfValidatorSchemaCompare(
                    'new_password',
                    sfValidatorSchemaCompare::EQUAL, 'new_password_confirmation',
                    array(),
                    array('invalid' => 'Пароли не совпадают')
                ),
                new sfValidatorDoctrineUnique(array('model' => 'User', 'column' => array('email')), array('invalid' => 'Такой пользователь уже есть'))
            )));
        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
            $validator->setOption('trim', true);
        }
    }
}
