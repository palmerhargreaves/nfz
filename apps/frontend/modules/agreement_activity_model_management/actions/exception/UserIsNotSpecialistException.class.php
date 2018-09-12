<?php

/**
 * Description of UserIsNotSpecialistException
 *
 * @author Сергей
 */
class UserIsNotSpecialistException extends sfException
{
  function __construct(User $user)
  {
    parent::__construct("User '{$user->getEmail()}' is not specialist");
  }
}
