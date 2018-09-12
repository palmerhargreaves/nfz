<?php

/**
 * Base class of an activity module descriptor
 *
 * @author Сергей
 */
abstract class ActivityModuleDescriptor
{
  // статусы выстроены в порядке важности
  const STATUS_NONE = 0;
  const STATUS_IMPORTANCE = 1;
  const STATUS_ACCEPTED = 2;
  const STATUS_WAIT_AGREEMENT = 3;
  const STATUS_WAIT_DEALER = 4;
  
  /**
   * Activity
   *
   * @var Activity
   */
  protected $activity;
  /**
   * User
   *
   * @var User
   */
  protected $user;
  
  function __construct(Activity $activity, User $user)
  {
    $this->activity = $activity;
    $this->user = $user;
  }
  
  /**
   * Returns additional content for an activity page.
   * Returns false if the module has not content.
   * 
   * @return mixed
   */
  function getActivityAdditional()
  {
    return false;
  }
  
  /**
   * Returns additional tabs for activity page:
   * array(
   *   identifier => array(
   *     'name' => tab name,
   *     'uri' => internal uri
   *   ),
   * ...
   * )
   * 
   * @return array
   */
  abstract function getActivityTabs();
  
  /**
   * Returns source uri or false if source is not found
   * 
   * @param LogEntry $entry
   * @return boolean|string
   */
  function getSourceUri(LogEntry $entry)
  {
    return false;
  }
  
  /**
   * Returns true if a module has additional configuration
   * 
   * @return boolean
   */
  function hasAdditionalConfiguration()
  {
    return false;
  }
  
  /**
   * Returns uri to additional configuration
   * 
   * @return string
   */
  function getAdditionalConfigurationUri()
  {
    return '';
  }
  
  function getStatus()
  {
    return $this->activity->getImportance() ? self::STATUS_IMPORTANCE : self::STATUS_NONE;
  }
}
