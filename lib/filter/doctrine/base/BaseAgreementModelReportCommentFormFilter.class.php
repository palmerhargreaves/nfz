<?php

/**
 * AgreementModelReportComment filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelReportCommentFormFilter extends AgreementCommentFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['report_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Report'), 'add_empty' => true));
    $this->validatorSchema['report_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Report'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('agreement_model_report_comment_filters[%s]');
  }

  public function getModelName()
  {
    return 'AgreementModelReportComment';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'report_id' => 'ForeignKey',
    ));
  }
}
