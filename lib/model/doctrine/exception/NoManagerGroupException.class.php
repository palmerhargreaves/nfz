<?php

/**
 * Description of NoManagerGroupException
 *
 * @author Сергей
 */
class NoManagerGroupException extends NoGroupException
{
  function __construct()
  {
    parent::__construct('Manager');
  }
}
