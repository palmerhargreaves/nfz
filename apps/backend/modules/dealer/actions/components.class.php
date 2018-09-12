<?php

class dealerComponents extends sfComponents
{
  function executeSelectDealers()
  {
    $this->dealers = DealerTable::getVwDealersQuery()->execute();
  }
}
