<?php

/**
 * Description of AgreementModelDateFieldRenderer
 *
 * @author Сергей
 */
class AgreementModelDateFieldRenderer extends AgreementModelFieldRenderer
{
  public function render()
  {
    return <<<MARKUP
<div class="modal-input-wrapper">
  <input type="text" name="{$this->getFieldName()}" id="{$this->getFieldName()}" class="date" placeholder="" data-format-expression="^[0-9]{2}(\.[0-9]{2}){2}$" data-required="{$this->getRequiredValue()}" data-right-format="21.01.13"/>
  <div class="modal-input-error-icon error-icon"></div>
  <div class="error message"></div>
</div>
MARKUP;
  }
}
