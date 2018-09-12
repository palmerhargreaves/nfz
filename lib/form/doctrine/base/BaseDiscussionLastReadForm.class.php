<?php

/**
 * DiscussionLastRead form base class.
 *
 * @method DiscussionLastRead getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDiscussionLastReadForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'message_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Message'), 'add_empty' => false)),
      'user_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'message_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Message'))),
      'user_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
    ));

    $this->widgetSchema->setNameFormat('discussion_last_read[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DiscussionLastRead';
  }

}
