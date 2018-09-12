<?php

/**
 * AgreementComment form base class.
 *
 * @method AgreementComment getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAgreementCommentForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'user_id'    => new sfWidgetFormInputText(),
      'status'     => new sfWidgetFormChoice(array('choices' => array('wait' => 'wait', 'accepted' => 'accepted', 'declined' => 'declined'))),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'    => new sfValidatorInteger(),
      'status'     => new sfValidatorChoice(array('choices' => array(0 => 'wait', 1 => 'accepted', 2 => 'declined'))),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('agreement_comment[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgreementComment';
  }

}
