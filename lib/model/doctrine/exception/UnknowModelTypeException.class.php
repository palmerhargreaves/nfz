<?php

/**
 * Description of UnknowModelTypeException
 *
 * @author Сергей
 */
class UnknowModelTypeException extends sfException
{
  function __construct()
  {
    parent::__construct('Unknow model type');
  }
}
