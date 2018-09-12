<?php

/**
 * AgreementModelComment form base class.
 *
 * @method AgreementModelComment getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelCommentForm extends AgreementCommentForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['model_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Model'), 'add_empty' => false));
    $this->validatorSchema['model_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Model')));

    $this->widgetSchema->setNameFormat('agreement_model_comment[%s]');
  }

  public function getModelName()
  {
    return 'AgreementModelComment';
  }

}
