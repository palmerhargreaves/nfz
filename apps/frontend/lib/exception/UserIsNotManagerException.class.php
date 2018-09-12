<?php

/**
 * Description of UserIsNotManagerException
 *
 * @author Сергей
 */
class UserIsNotManagerException extends sfException
{
  function __construct(User $user)
  {
    parent::__construct("User '{$user->getEmail()}' is not a dealer manager");
  }
}
