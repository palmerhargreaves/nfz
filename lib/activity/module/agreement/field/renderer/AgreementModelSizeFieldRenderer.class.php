<?php

/**
 * Description of AgreementModelSizeFieldRenderer
 *
 * @author kostet
 */
class AgreementModelSizeFieldRenderer extends AgreementModelFieldRenderer
{
  private static $end_field_counter = 0;
  private $_editorLink = false;
  
  public function render()
  {
    $defValue1 = '';
    $defValue2 = '';
    $canEdit = '';

    if($this->_editorLink) {
        $defValue = explode('x', $this->getField()->getDefValue());
        $canEdit = !$this->getField()->getEditable() ? "disabled" : "";

        if(count($defValue) > 1 && isset($defValue[0]) && isset($defValue[1])) {
          $defValue1 = $defValue[0];
          $defValue2 = $defValue[1];
        }
    }

    $end_field_id = self::generateEndFieldId();
    return <<<MARKUP
<div class="modal-input-group-wrapper size-group" id="{$this->getFieldName()}">
  <input type="hidden" name="{$this->getFieldName()}"/>
  <div class="modal-input-wrapper modal-short-input-wrapper">
    <input type="text" name="_{$this->getFieldName()}[start]" class="size-field" value="{$defValue1}" {$canEdit} data-value="{$defValue1}" data-format-expression="^[0-9\,\.]+$" data-required="{$this->getRequiredValue()}" data-right-format="" data-message-selector="#{$end_field_id}"/>
    <div class="modal-input-error-icon error-icon"></div>
  </div>
  <span style="font-size: 10px; margin: 1px; margin-top: 11px; margin-left: 3px; position: relative; float: left;">x</span>
  <div class="modal-input-wrapper modal-short-input-wrapper">
    <input type="text" name="_{$this->getFieldName()}[end]" class="size-field" value="{$defValue2}" {$canEdit} data-value="{$defValue2}" data-format-expression="^[0-9\,\.]+$" data-required="{$this->getRequiredValue()}" data-right-format=""/>
    <div class="modal-input-error-icon error-icon"></div>
    <div class="error message" id="$end_field_id"></div>
  </div>
</div>
MARKUP;
  }

  public function setEditorLink($editorLink) {
    $this->_editorLink = $editorLink;
  }
  
  private static function generateEndFieldId()
  {
    return 'size_end_field_'.(self::$end_field_counter ++);
  }
}
