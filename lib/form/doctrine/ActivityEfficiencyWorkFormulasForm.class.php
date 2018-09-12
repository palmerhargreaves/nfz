<?php

/**
 * ActivityEfficiencyWorkFormulas form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ActivityEfficiencyWorkFormulasForm extends BaseActivityEfficiencyWorkFormulasForm
{
    public function configure()
    {
        $this->widgetSchema['position'] = new sfWidgetFormInputHidden();

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

}
