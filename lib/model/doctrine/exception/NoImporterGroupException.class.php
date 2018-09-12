<?php

/**
 * Description of NoImporterGroupException
 *
 * @author Сергей
 */
class NoImporterGroupException extends NoGroupException
{
  function __construct()
  {
    parent::__construct('Importer');
  }
}
