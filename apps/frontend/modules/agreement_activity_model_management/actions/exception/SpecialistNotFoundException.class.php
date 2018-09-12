<?php

/**
 * Description of SpecialistNotFoundException
 *
 * @author Сергей
 */
class SpecialistNotFoundException extends sfException
{
  function __construct($group_id, $user_id)
  {
    parent::__construct("Specialist ($user_id) is not found for group: $group_id");
  }
}
