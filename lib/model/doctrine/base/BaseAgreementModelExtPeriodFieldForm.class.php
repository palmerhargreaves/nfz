<?php

/**
 * AgreementModelPeriodField form base class.
 *
 * @method AgreementModelPeriodField getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelExtPeriodFieldForm extends AgreementModelFieldForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('agreement_model_period_field[%s]');
  }

  public function getModelName()
  {
    return 'AgreementModelExtPeriodField';
  }

}
