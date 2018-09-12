<?php

/**
 * Description of WidgetFormFile
 *
 * @author Сергей
 */
class WidgetFormFile extends sfWidgetFormInputFileEditable
{
  function __construct($options = array(), $attributes = array())
  {
    $options['template'] = '%input%<br />%delete% %delete_label%';
    
    parent::__construct($options, $attributes);
  }
  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $input = parent::render($name, $value, $attributes, $errors);
    
    if($value)
      $input = '<div><a href="'.$this->getOption('file_src').'/'.$value.'" target="_blank">загрузить</a></div>'.$input;
    
    return $input;
  }
}
