<?php

/**
 * Factory of module descriptorы
 *
 * @author Сергей
 */
class ActivityModuleDescriptorFactory
{
  static private $instance = null;
  
  /**
   * Returns an instance of the factory
   * 
   * @return ActivityModuleDescriptorFactory
   */
  static function getInstance()
  {
    if(!self::$instance)
    {
      self::$instance = new ActivityModuleDescriptorFactory();
    }
    return self::$instance;
  }

  /**
   * Creates an instance of module for the passed activity.
   * 
   * @param ActivityModule $module
   * @param Activity $activity
   * @param User $user
   * @return ActivityModuleDescriptor
   * @throws ActivityHasNotModuleException
   */
  function create(ActivityModule $module, Activity $activity, User $user)
  {
    $class = ucfirst($module->getIdentifier()).'ActivityModuleDescriptor';
    return new $class($activity, $user);
  }
  
  /**
   * Creates an instance of module descriptor for the passed activity.
   * Alias of create().
   * 
   * @param ActivityModule $module
   * @param Activity $activity
   * @param User $user
   * @return ActivityModuleDescriptor
   * @throws ActivityHasNotModuleException
   */
  static function descriptor(ActivityModule $module, Activity $activity, User $user)
  {
    return self::getInstance()->create($module, $activity, $user);
  }
}
