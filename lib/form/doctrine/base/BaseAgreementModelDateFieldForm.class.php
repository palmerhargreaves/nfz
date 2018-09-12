<?php

/**
 * AgreementModelDateField form base class.
 *
 * @method AgreementModelDateField getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelDateFieldForm extends AgreementModelFieldForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('agreement_model_date_field[%s]');
  }

  public function getModelName()
  {
    return 'AgreementModelDateField';
  }

}
