<?php

/**
 * ActivityModule
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ActivityModule extends BaseActivityModule
{
  /**
   * Returns a history processor of this module
   * 
   * @return HistoryProcessor
   */
  function getHistoryProcessor()
  {
    return HistoryProcessorFactory::getInstance()->getProcessor($this->getIdentifier());
  }
  
  /**
   * Returns module by its identifier
   * 
   * @param string $identifier
   * @return ActivityModule|false 
   */
  static function byIdentifier($identifier)
  {
    return ActivityModuleTable::getInstance()->findOneByIdentifier($identifier);
  }
}