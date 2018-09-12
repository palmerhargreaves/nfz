<?php

/**
 * AgreementModelComment filter form base class.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAgreementModelCommentFormFilter extends AgreementCommentFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['model_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Model'), 'add_empty' => true));
    $this->validatorSchema['model_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Model'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('agreement_model_comment_filters[%s]');
  }

  public function getModelName()
  {
    return 'AgreementModelComment';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'model_id' => 'ForeignKey',
    ));
  }
}
