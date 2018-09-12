<?php

/**
 * Description of RecoveryPasswordForm
 *
 * @author Сергей
 */
class RecoveryPasswordForm extends BaseForm
{
  function configure()
  {
    $this->setWidgets(array(
      'email' => new sfWidgetFormInputText()
    ));
    
    $this->setValidators(array(
      'email' => new sfValidatorAnd(array(
        new sfValidatorEmail(),
        new sfValidatorCallback(array(
          'callback' => array($this, 'validateForUserExist')
        ))
      )) 
    ));
    
    foreach ($this->validatorSchema->getFields() as $validator)
    {
      $validator->setMessage('required', 'Обязательно для заполнения');
    }
  } 
  
  function validateForUserExist($validator, $value)
  {
    if(!UserTable::getInstance()->isUserActive($value))
    {
      $validator->setMessage('invalid', 'Пользователь с таким e-mail не найден');
      throw new sfValidatorError($validator, 'invalid');
    }
    
    return $value;
  }
}
