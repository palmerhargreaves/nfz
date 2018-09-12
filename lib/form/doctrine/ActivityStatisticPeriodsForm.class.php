<?php

/**
 * ActivityStatisticPeriodsForm form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ActivityStatisticPeriodsForm extends BaseActivityStatisticPeriodsForm
{
  public function configure()
  {
    $this->widgetSchema['activity_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['activity_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity')));
    
    $year = range(2010, date('Y') + 10);
    $years = array_merge(array(''), array_combine($year, $year));

    $this->widgetSchema['year'] = new sfWidgetFormChoice(array('choices' => $years));
    $this->validatorSchema['year'] = new sfValidatorChoice(array('required' => true, 'choices' => array_keys($years)));

    foreach ($this->validatorSchema->getFields() as $validator)
    {
      $validator->setMessage('required', 'Обязательно для заполнения');
    }
  }
}
