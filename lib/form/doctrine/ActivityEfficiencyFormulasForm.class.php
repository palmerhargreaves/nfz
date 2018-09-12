<?php

/**
 * ActivityEfficiencyFormulas form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ActivityEfficiencyFormulasForm extends BaseActivityEfficiencyFormulasForm
{
    public function configure()
    {
        $this->widgetSchema['activity_id'] = new sfWidgetFormInputHidden();
        $this->widgetSchema['name'] = new sfWidgetFormInputHidden();

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

}
