<?php

/**
 * Helps to copy discussion messages to the log
 *
 * @author Сергей
 */
class DealerDiscussionLogger
{
  function copy()
  {
    $last_id = VariableTable::getInstance()->getValue('last_discussion_id', 0);
    $new_messages = MessageTable::getInstance()
                    ->createQuery('m')
                    ->innerJoin('m.Discussion d')
                    ->innerJoin('d.DealerDiscussions dd')
                    ->leftJoin('m.User u')
                    ->where('m.id>?', $last_id)
                    ->orderBy('id ASC')
                    ->execute();
    
    foreach($new_messages as $message)
    {
      $entry = new LogEntry();
      $has_files = MessageFileTable::getInstance()
                   ->createQuery()
                   ->where('message_id=?', $message->getId())
                   ->count() > 0;
        
      $entry->setArray(array(
        'user_id' => $message->getUserId(),
        'login' => $message->getUser() ? $message->getUser()->getEmail() : $message->getUserName(),
        'title' => 'Задать вопрос',
        'description' => $message->getText(),
        'icon' => $has_files ? 'clip' : '',
        'object_id' => $message->getId(),
        'object_type' => 'ask',
        'action' => 'post',
        'dealer_id' => $message->getDiscussion()->getDealerDiscussions()->offsetGet(0)->getDealerId(),
        'message_id' => $message->getId(),
        'created_at' => $message->created_at,
        'private_user_id' => $message->getPrivateUserId()
      ));
      $entry->save();
      
      $this->addReadingForDiscussionParticipants($entry, $message);
      
      $last_id = $message->getId();
    }
    
    VariableTable::getInstance()->setValue('last_discussion_id', $last_id);
  }
  
  protected function addReadingForDiscussionParticipants(LogEntry $entry, Message $message)
  {
    $last_reads = DiscussionLastReadTable::getInstance()
                  ->createQuery('lr')
                  ->select('lr.*, u.id')
                  ->innerJoin('lr.User u')
                  ->innerJoin('lr.Message m WITH m.id>=?', $message->getId())
                  ->innerJoin('m.Discussion d WITH d.id=?', $message->getDiscussionId())
                  ->execute();

    $users = array();
    foreach($last_reads as $last_read)
    {
      // обход возможной ситуации, когда в DiscussionLastRead окажется дубликат
      if(!isset($users[$last_read->getUserId()]))
      {
        LogEntryReadTable::getInstance()->addRead($last_read->getUser(), $entry);
        $users[$last_read->getUserId()] = true;
      }
    }
  }
}
