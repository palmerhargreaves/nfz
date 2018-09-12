<?php
/**
 * Description of AuthForm
 *
 * @author Сергей
 */
class AuthForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'login' => new sfWidgetFormInputText(array('label' => 'E-mail')),
      'password' => new sfWidgetFormInputPassword(array('label' => 'Пароль')),
      'remember' => new sfWidgetFormInputCheckbox(array('label' => 'Запомнить'))
    ));
    
    $this->setValidators(array(
      'login' => new sfValidatorString(array('required' => true)),
      'password' => new sfValidatorString(array('required' => true)),
      'remember' => new sfValidatorBoolean()
    ));
    
    $this->getValidatorSchema()->setPostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'validateLoginPassword')
    ), array('invalid' => 'Неверный логин или пароль')));
    
    foreach ($this->validatorSchema->getFields() as $validator)
    {
      $validator->setMessage('required', 'Обязательно для заполнения');
    }
  }
  
  public function validateLoginPassword($validator, $values)
  {
    if($values['login'] && $values['password'])
    {
      if(!AuthFactory::getInstance()->getAuthenticator()->auth($values['login'], $values['password']))
        throw new sfValidatorError($validator, 'invalid');
    }
    
    return $values;
  }
}
