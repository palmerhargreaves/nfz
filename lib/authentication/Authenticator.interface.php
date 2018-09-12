<?php
/**
 * Interface of an authenticator
 *
 * @author Сергей
 */
interface Authenticator
{
  function auth($login, $password);
  
  function setupPassword(User $user, $password);
}
