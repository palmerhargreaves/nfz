<?php

/**
 * Description of AgreementModelFieldRenderer
 *
 * @author Сергей
 */
abstract class AgreementModelFieldRenderer
{
  /**
   * Field
   *
   * @var AgreementModelField
   */
  protected $field;
  
  function __construct(AgreementModelField $field)
  {
    $this->field = $field;
  }
  
  /**
   * Returns field
   * 
   * @return AgreementModelField
   */
  function getField()
  {
    return $this->field;
  }
  
  function getFieldName()
  {
    return $this->field->getModelType()->getIdentifier().'['.$this->field->getIdentifier().']';
  }
  
  protected function getRequiredValue()
  {
    return $this->field->getRequired() ? 'true' : 'false';
  }
  
  abstract function render();
}
