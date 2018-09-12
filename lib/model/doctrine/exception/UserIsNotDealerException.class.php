<?php

/**
 * Description of UserIsNotDealerException
 *
 * @author Сергей
 */
class UserIsNotDealerException extends sfException
{
  function __construct(User $user)
  {
    parent::__construct('User "'.$user->getEmail().'" is not a dealer');
  }
}
