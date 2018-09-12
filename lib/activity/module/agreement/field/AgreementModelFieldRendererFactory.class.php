<?php

/**
 * Description of AgreementModelFieldRendererFactory
 *
 * @author Сергей
 */
class AgreementModelFieldRendererFactory
{
  private static $instance = null;
  
  /**
   * Returns sn instance of AgreementModelFieldRendererFactory
   * 
   * @return AgreementModelFieldRendererFactory
   */
  static function getInstance()
  {
    if(!self::$instance)
    {
      self::$instance = new AgreementModelFieldRendererFactory();
    }
    return self::$instance;
  }
  
  /**
   * Creates a field renderer
   * 
   * @param AgreementModelField $field
   * @return AgreementModelFieldRenderer
   */
  function create(AgreementModelField $field)
  {
    $class = 'AgreementModel'.ucfirst($field->getType()).'FieldRenderer';

    if($field->getIdentifier() == 'size')
      $class = 'AgreementModel'.ucfirst($field->getIdentifier()).'FieldRenderer';
        
    return new $class($field);
  }
}
