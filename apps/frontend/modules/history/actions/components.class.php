<?php
class historyComponents extends sfComponents
{
  function executeUnread()
  {
    $this->count = LogEntryTable::getInstance()->countUnread(
      $this->getUser()->getAuthUser(), 
      !$this->getUser()->isManager() && $this->getUser()->isDealerUser() ? $this->getUser()->getAuthUser()->getDealer() : null,
      !$this->getUser()->isManager() && $this->getUser()->isSpecialist()
    );
    
    if($this->count > 0)
      $this->outputLastHistory($this->count);
  }
  
  protected function outputLastHistory($unread_count)
  {
    $query = LogEntryTable::getInstance()
             ->createQuery('l')
             ->orderBy('created_at desc, id desc')
             ->limit($unread_count < 3 ? $unread_count : 3);
    
    LogEntryTable::applyConditionsToSkipUnreadableEntries(
      $query, 
      $this->getUser()->getAuthUser(), 
      !$this->getUser()->isManager() && $this->getUser()->isSpecialist()
    );
    
    if(!$this->getUser()->isManager() && $this->getUser()->isDealerUser())
      $query->andWhere('l.dealer_id=? or l.dealer_id=0', $this->getUser()->getAuthUser()->getDealer()->getId());
    
    $this->last_history = $query->execute();    
  }
}