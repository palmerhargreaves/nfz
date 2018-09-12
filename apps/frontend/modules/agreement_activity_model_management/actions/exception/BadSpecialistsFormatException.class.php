<?php

/**
 * Description of BadSpecialistsFormatException
 *
 * @author Сергей
 */
class BadSpecialistsFormatException extends sfException
{
  function __construct()
  {
    parent::__construct("Bad format of specialists data");
  }
}
