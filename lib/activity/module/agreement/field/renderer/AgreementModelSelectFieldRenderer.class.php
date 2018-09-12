<?php

/**
 * Description of AgreementModelSelectFieldRenderer
 *
 * @author Сергей
 */
class AgreementModelSelectFieldRenderer extends AgreementModelFieldRenderer
{
  public function render()
  {
    $options = explode(';', $this->field->getList());
    $first = isset($options[0]) ? $options[0] : '';
    $markup = '<div class="modal-select-wrapper select input krik-select">';
    $markup .= '<span class="select-value">'.$first.'</span>';
    $markup .= '<div class="ico"></div>';
    $markup .= '<input type="hidden" name="'.$this->getFieldName().'" value="'.$first.'">';
    $markup .= '<div class="modal-input-error-icon error-icon"></div>';
    $markup .= '<div class="error message"></div>';
    $markup .= '<div class="modal-select-dropdown">';
    foreach($options as $option)
      $markup .= "<div class=\"modal-select-dropdown-item select-item\" data-value=\"$option\">$option</div>";
      
    $markup .= '</div>';
    $markup .= '</div>';
    
    return $markup;
  }
}
