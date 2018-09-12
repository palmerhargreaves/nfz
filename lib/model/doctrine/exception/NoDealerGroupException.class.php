<?php

/**
 * Description of NoDealerGroupException
 *
 * @author Сергей
 */
class NoDealerGroupException extends NoGroupException
{
  function __construct()
  {
    parent::__construct('Dealer');
  }
}
