<?php

/**
 * ActivityInfo form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ActivityInfoForm extends BaseActivityInfoForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at']);
    
    $this->widgetSchema['activity_id'] = new sfWidgetFormInputHidden();
    
    $icons = array_merge(array(''), F::getFiles(sfConfig::get('sf_web_dir').'/images/info/'));
    $icons = array_combine($icons, $icons);
    $this->widgetSchema['icon'] = new sfWidgetFormChoice(array(
        'label' => 'Иконка',
        'choices' => $icons
    ));
    
    $this->validatorSchema['icon'] = new sfValidatorChoice(array(
      'choices' => array_keys($icons),
    ));
    
    foreach ($this->validatorSchema->getFields() as $validator)
    {
      $validator->setMessage('required', 'Обязательно для заполнения');
    }
  }
}
