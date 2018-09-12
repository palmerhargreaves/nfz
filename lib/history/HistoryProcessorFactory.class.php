<?php

/**
 * Description of HistoryProcessorFactory
 *
 * @author Сергей
 */
class HistoryProcessorFactory
{
  private static $instance = null;
  
  /**
   * Returns an instance of the factory
   * 
   * @return HistoryProcessorFactory
   */
  static function getInstance()
  {
    if(!self::$instance)
    {
      self::$instance = new HistoryProcessorFactory();
    }
    return self::$instance;
  }
  
  /**
   * Returns a processor by name
   * 
   * @param string $name
   * @return HistoryProcessor
   */
  function getProcessor($name)
  {
    $class = ucfirst($name).'HistoryProcessor';
    return new $class();
  }
}
