<?php

/**
 * Description of UserIsNotAuthenticatedException
 *
 * @author Сергей
 */
class UserIsNotAuthenticatedException extends sfException
{
  function __construct()
  {
    parent::__construct("User is not authenticated");
  }
}
