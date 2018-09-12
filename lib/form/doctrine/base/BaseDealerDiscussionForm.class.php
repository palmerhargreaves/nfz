<?php

/**
 * DealerDiscussion form base class.
 *
 * @method DealerDiscussion getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDealerDiscussionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'dealer_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => false)),
      'discussion_id' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'dealer_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'))),
      'discussion_id' => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('dealer_discussion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DealerDiscussion';
  }

}
