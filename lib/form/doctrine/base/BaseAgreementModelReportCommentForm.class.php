<?php

/**
 * AgreementModelReportComment form base class.
 *
 * @method AgreementModelReportComment getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelReportCommentForm extends AgreementCommentForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['report_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Report'), 'add_empty' => false));
    $this->validatorSchema['report_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Report')));

    $this->widgetSchema->setNameFormat('agreement_model_report_comment[%s]');
  }

  public function getModelName()
  {
    return 'AgreementModelReportComment';
  }

}
