<?php

/**
 * DiscussionOnline form base class.
 *
 * @method DiscussionOnline getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDiscussionOnlineForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'discussion_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Discussion'), 'add_empty' => false)),
      'user_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'discussion_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Discussion'))),
      'user_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('discussion_online[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DiscussionOnline';
  }

}
