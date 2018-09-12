<?php
/**
 * Description of AuthForm
 *
 * @author Сергей
 */
class ChangePasswordForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'old_password' => new sfWidgetFormInputPassword(array('label' => 'Старый пароль')),
      'new_password' => new sfWidgetFormInputPassword(array('label' => 'Новый пароль')),
    ));
    
    $this->setValidators(array(
      'old_password' => new sfValidatorAnd(array(
        new sfValidatorString(),
        new sfValidatorCallback(array(
          'callback' => array($this, 'validatePassword')
        ))
      ), array(), array(
        'required' => 'Обязательно для заполнения'
      )),
      'new_password' => new sfValidatorString(
        array(), array('required' => 'Обязательно для заполнения')
      ),
    ));
  }
  
  public function validatePassword(sfValidatorCallback $validator, $value)
  {
    if(!AuthFactory::getInstance()->getAuthenticator()->auth(sfContext::getInstance()->getUser()->getAuthUser()->getEmail(), $value))
    {
      $validator->setMessage('invalid', 'Неверный пароль');
      throw new sfValidatorError($validator, 'invalid');
    }
      
    return $value;
  }
}
