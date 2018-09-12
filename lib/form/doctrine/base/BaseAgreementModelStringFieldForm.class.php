<?php

/**
 * AgreementModelStringField form base class.
 *
 * @method AgreementModelStringField getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelStringFieldForm extends AgreementModelFieldForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('agreement_model_string_field[%s]');
  }

  public function getModelName()
  {
    return 'AgreementModelStringField';
  }

}
