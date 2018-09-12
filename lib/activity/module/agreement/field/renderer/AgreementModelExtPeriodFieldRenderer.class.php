<?php

/**
 * Description of AgreementModelExtPeriodFieldRenderer
 *
 * @author Сергей
 */
class AgreementModelExtPeriodFieldRenderer extends AgreementModelFieldRenderer
{
  private static $end_field_counter = 0;
  
  public function render()
  {
    $end_field_id = self::generateEndFieldId();
    return <<<MARKUP
<div class="modal-input-group-wrapper period-group" id="{$this->getFieldName()}">
  <input type="hidden" name="{$this->getFieldName()}"/>
  <div class="modal-input-wrapper modal-short-input-wrapper">
    <input type="text" name="_{$this->getFieldName()}[start]" class="date-ext" placeholder="от" data-format-expression="^[0-9]{2}(\.[0-9]{2}){2}$" data-required="{$this->getRequiredValue()}" data-right-format="21.01.13" data-message-selector="#{$end_field_id}"/>
    <div class="modal-input-error-icon error-icon"></div>
  </div>
  <div class="modal-input-wrapper modal-short-input-wrapper">
    <input type="text" name="_{$this->getFieldName()}[end]" class="date-ext" placeholder="до" data-format-expression="^[0-9]{2}(\.[0-9]{2}){2}$" data-required="{$this->getRequiredValue()}" data-right-format="21.01.13"/>
    <div class="modal-input-error-icon error-icon"></div>
    <div class="error message" id="$end_field_id"></div>
  </div>
</div>
MARKUP;
  }
  
  private static function generateEndFieldId()
  {
    return 'end_field_'.(self::$end_field_counter ++);
  }
}
