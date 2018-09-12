<?php

/**
 * Budget form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class BudgetForm extends BaseBudgetForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at']);
    
    $this->widgetSchema['dealer_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['quarter'] = new sfWidgetFormChoice(array(
      'choices' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4)
    ));
    
    $this->validatorSchema['quarter'] = new sfValidatorChoice(
      array('choices' => array(1, 2, 3, 4))
    );
    
    foreach ($this->validatorSchema->getFields() as $validator)
    {
      $validator->setMessage('required', 'Обязательно для заполнения');
    }
  }
}
