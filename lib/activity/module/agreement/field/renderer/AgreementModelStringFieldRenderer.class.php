<?php

/**
 * Description of AgreementModelStringFieldRenderer
 *
 * @author Сергей
 */
class AgreementModelStringFieldRenderer extends AgreementModelFieldRenderer
{
  public function render()
  {
    return <<<MARKUP
<div class="modal-input-wrapper">
  <input type="text" name="{$this->getFieldName()}" id="{$this->getFieldName()}" placeholder="{$this->field->getFormatHint()}" data-format-expression="{$this->field->getFormatExpression()}" data-required="{$this->getRequiredValue()}" data-right-format="{$this->field->getRightFormat()}"/>
  <div class="modal-input-error-icon error-icon"></div>
  <div class="error message"></div>
</div>
MARKUP;
  }
}
