<?php

/**
 * Description of ActionDoesNotMatchModuleException
 *
 * @author Сергей
 */
class ActionDoesNotMatchModuleException extends sfException
{
  function __construct()
  {
    parent::__construct('Action does not match the module');
  }
}
