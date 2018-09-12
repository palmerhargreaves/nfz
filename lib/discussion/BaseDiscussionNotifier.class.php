<?php

/**
 * Description of BaseDiscussionNotifier
 *
 * @author Сергей
 */
abstract class BaseDiscussionNotifier
{
  protected $object_type;
  protected $notifications = array();
  protected $users = array();
  protected $notify_all_participants;
  
  function __construct($object_type, $notify_all_participants = false)
  {
    $this->object_type = $object_type;
    $this->notify_all_participants = $notify_all_participants;
  }
  
  function notify()
  {
    $this->notifications = array();
    
    $last_id_var_name = 'last_'.$this->object_type.'_notify_id';
    $last_id = VariableTable::getInstance()->getValue($last_id_var_name, 0);
    
    $entries = LogEntryTable::getInstance()
               ->createQuery('l')
               ->innerJoin('l.User u')
               ->where('l.object_type=? and l.action=? and l.id>?', array($this->object_type, 'post', $last_id))
               ->orderBy('id ASC')
               ->execute(); 
    
    echo "found entries: ", count($entries), "\r\n";
    
    foreach($entries as $entry)
    {
      $this->addNotification($entry);
      $last_id = $entry->getId();
    }
    
    $this->sendNotifications();
    
    VariableTable::getInstance()->setValue($last_id_var_name, $last_id);
  }
  
  protected function sendNotifications()
  {
    foreach($this->notifications as $user_id => $entries)
    {
      $user = $this->getUser($user_id);
      if(!$user)
        continue;
      
      $unread = $this->filterReadMessages($user, $entries);
      if(!$unread)
        continue;
      
      echo "send to ", $user->getEmail(), "\r\n";
      $message = $this->getMessage($user, $unread);
      $message->setPriority(1);
      sfContext::getInstance()->getMailer()->send($message);
    }
  }
  
  protected function filterReadMessages(User $user, $entries)
  {
    // for PHP 5.2
    $new_entries = array();
    foreach($entries as $entry)
    {
      if(!$this->isRead($user, $entry))
        $new_entries[] = $entry;
    }
    return $new_entries;
    
//    return array_filter($entries, function($entry) use ($user) {
//      return !$this->isRead($user, $entry);
//    });
  }
  
  protected function isRead(User $user, LogEntry $entry)
  {
    $discussion = $this->getDiscussion($entry);
    
    if(!$discussion)
      return true;
    
    return $discussion->getLastRead($user)->getMessageId() >= $entry->getObjectId();
  }
  
  protected function addNotification(LogEntry $entry)
  {
    if($entry->getPrivateUserId())
    {
      $this->addPrivateNotification($entry);
    }
    else
    {
      if($entry->getDealerId())
        $this->addDealerNotification($entry);
      
      $this->addManagerNotification($entry);
      
      if($this->notify_all_participants)
        $this->addParticipantNotification($entry);
    }
  }
  
  protected function addPrivateNotification(LogEntry $entry)
  {
//    echo "private notification\r\n";
    
    $this->addNotificationForUser($entry, $entry->getPrivateUserId());
  }
  
  protected function addDealerNotification(LogEntry $entry)
  {
    $dealer_users = UserTable::getInstance()
                    ->createQuery('u')
                    ->innerJoin('u.DealerUsers du WITH dealer_id=?', $entry->getDealerId())
                    ->where('active=?', true)
                    ->execute();
    
//    echo "found dealers: ", count($dealer_users), "\r\n";
    
    foreach($dealer_users as $user)
    {
      $this->users[$user->getId()] = $user;
      $this->addNotificationForUser($entry, $user->getId());
    }
  }
  
  protected function addManagerNotification(LogEntry $entry)
  {
    foreach($this->getManagersQueryToNotify()->execute() as $manager)
    {
//      echo "manager: ", $manager->getEmail(), "\r\n";
      
      $this->users[$manager->getId()] = $manager;
      $this->addNotificationForUser($entry, $manager->getId());
    }
  }
  
  protected function getManagersQueryToNotify()
  {
    return UserTable::getInstance()
           ->createQuery('u')
           ->innerJoin('u.Group g')
           ->innerJoin('g.Roles r WITH r.role=?', 'manager')
           ->where('active=?', true);
  }
  
  protected function addParticipantNotification(LogEntry $entry)
  {
    $discussion = $this->getDiscussion($entry);
    if(!$discussion)
      return;
    
//    echo "found discussion participants: ", count($discussion->getActiveParticipants()), "\r\n";
    
    foreach($discussion->getActiveParticipants() as $participant)
    {
      $this->users[$participant->getId()] = $participant;
      $this->addNotificationForUser($entry, $participant->getId());
    }
  }
  
  protected function addNotificationForUser(LogEntry $entry, $user_id)
  {
    if(!isset($this->notifications[$user_id]))
      $this->notifications[$user_id] = array();
    
    // контролируем уникальность по id сообщения, а не записи в логе
    $this->notifications[$user_id][$entry->getObjectId()] = $entry;
  }
  
  /**
   * Returns an user
   * 
   * @param int $user_id
   * @return User 
   */
  protected function getUser($user_id)
  {
    if(!isset($this->users[$user_id]))
    {
      $this->users[$user_id] = UserTable::getInstance()->find($user_id);
    }
    return $this->users[$user_id];
  }
  
  /**
   * Returns a discussion by an entry
   * 
   * @param LogEntry $entry
   * @return Discussion|false 
   */
  protected function getDiscussion(LogEntry $entry)
  {
    return DiscussionTable::getInstance()
           ->createQuery('d')
           ->select('d.*')
           ->innerJoin('d.Messages m WITH m.id=?', $entry->getObjectId())
           ->fetchOne();    
  }
  
  /**
   * Returns array of mail messages
   * 
   * @param User $user
   * @param array $entries array of entries
   * @return DiscussionMail
   */
  abstract protected function getMessage(User $user, array $entries);
    
}
