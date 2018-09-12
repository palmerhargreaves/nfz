<?php

/**
 * Description of RegistrationForm
 *
 * @author Сергей
 */
class RegistrationForm extends BaseForm
{
    function configure()
    {
        $this->setWidgets(array(
            'email' => new sfWidgetFormInputText(array(
                'label' => 'E-mail'
            )),
            'password' => new sfWidgetFormInputPassword(array(
                'label' => 'Пароль'
            )),
            'fio' => new sfWidgetFormInputText(array(
                'label' => 'ФИО'
            )),
            'company_type' => new sfWidgetFormChoice(array(
                'choices' => array(
                    'dealer' => 'Дилер', 'importer' => 'Импортер', 'regional_manager' => 'Региональный менеджер', 'other' => 'Другое'
                )
            )),
            'dealer_id' => new sfWidgetFormDoctrineChoice(array(
                'model' => 'Dealer',
                'query' => DealerTable::getVwDealersQuery(),
                'label' => 'Ваше дилерское преприятие'
            )),
            'company_name' => new sfWidgetFormInputText(array(
                'label' => 'Компания'
            )),
            'post' => new sfWidgetFormInputText(array(
                'label' => 'Должность'
            )),
            'phone' => new sfWidgetFormInputText(array(
                'label' => 'Телефон'
            )),
            'mobile' => new sfWidgetFormInputText(array(
                'label' => 'Мобильный телефон'
            )),
            'agree' => new sfWidgetFormInputCheckbox(array(
                'label' => 'Я согласен на предоставление персональных данных'
            )),
        ));

        $this->setValidators(array(
            'email' => new sfValidatorAnd(array(
                new sfValidatorEmail(array(), array(
                    'required' => 'Обязательно для заполнения',
                    'invalid' => 'Неверный e-mail'
                )),
                new sfValidatorDoctrineUnique(array(
                    'model' => 'User',
                    'column' => 'email'
                ), array(
                    'invalid' => 'Такой e-mail уже зарегистрирован'
                ))
            )),
            'password' => new sfValidatorString(array(
                'required' => true
            )),
            'fio' => new sfValidatorCallback(array(
                'callback' => array($this, 'validateFio'),
                'required' => true
            )),
            'company_type' => new sfValidatorChoice(array(
                'choices' => array(
                    'dealer', 'importer', 'regional_manager', 'other'
                )
            )),
            'dealer_id' => new sfValidatorDoctrineChoice(array(
                'required' => false,
                'query' => DealerTable::getVwDealersQuery(),
                'model' => 'Dealer'
            )),
            'company_name' => new sfValidatorString(array(
                'required' => false
            )),
            'post' => new sfValidatorString(array(
                'required' => false
            )),
            'phone' => new sfValidatorString(array(
                'required' => true
            )),
            'mobile' => new sfValidatorString(array(
                'required' => false
            )),
            'agree' => new sfValidatorBoolean(array('required' => true)),
        ));

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

    function validateFio(sfValidatorBase $validator, $value)
    {
        $exploded = explode(' ', preg_replace('#\s{2,}#', ' ', $value));
        $fio = array();
        foreach ($exploded as $name) {
            $name = trim($name);
            if ($name)
                $fio[] = $name;
        }

        if (count($fio) < 3) {
            $validator->setMessage('invalid', 'ФИО должно состоять из 3 слов');
            throw new sfValidatorError($validator, 'invalid');
        }

        return implode(' ', $fio);
    }

    function getUserName()
    {
        $fio = explode(' ', $this->getValue('fio'));
        return $fio[1];
    }

    function getUserSurname()
    {
        $fio = explode(' ', $this->getValue('fio'));
        return $fio[0];
    }

    function getUserPatronymic()
    {
        $fio = explode(' ', $this->getValue('fio'));
        return $fio[2];
    }
}
