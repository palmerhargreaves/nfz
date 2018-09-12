<?php

/**
 * Description of NotFoundSpecialistForGroupException
 *
 * @author Сергей
 */
class NotFoundSpecialistForGroupException extends sfException
{
  function __construct($group_id)
  {
    parent::__construct("Specialist is not found for group: $group_id");
  }
}
