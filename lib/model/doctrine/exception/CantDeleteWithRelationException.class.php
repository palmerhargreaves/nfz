<?php

/**
 * Description of CantDeleteWithRelationException
 *
 * @author Сергей
 */
class CantDeleteWithRelationException extends sfException
{
  function __construct(Doctrine_Record $record)
  {
    parent::__construct(
      sprintf('Can\'t delete object "%s" with identifier(-s) "%s" since have related objects.', get_class($record), implode(',', $record->identifier()))
    );
  }
}
