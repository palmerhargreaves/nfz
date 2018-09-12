<?php

/**
 * Description of NoGroupException
 *
 * @author Сергей
 */
class NoGroupException extends sfException
{
  function __construct($role)
  {
    parent::__construct("$role group is not exist");
  }
}
