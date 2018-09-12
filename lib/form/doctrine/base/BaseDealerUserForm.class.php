<?php

/**
 * DealerUser form base class.
 *
 * @method DealerUser getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDealerUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'user_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'dealer_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'), 'add_empty' => false)),
      'approved'   => new sfWidgetFormInputCheckbox(),
      'manager'    => new sfWidgetFormInputCheckbox(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'dealer_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Dealer'))),
      'approved'   => new sfValidatorBoolean(array('required' => false)),
      'manager'    => new sfValidatorBoolean(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DealerUser', 'column' => array('user_id', 'dealer_id')))
    );

    $this->widgetSchema->setNameFormat('dealer_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DealerUser';
  }

}
