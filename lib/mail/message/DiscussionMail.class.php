<?php

/**
 * Description of DiscussionMail
 *
 * @author Сергей
 */
class DiscussionMail extends HistoryMail
{
  /**
   * Returns message by a log entry
   * 
   * @param LogEntry $entry
   * @return Message|false 
   */
  protected function getMessage(LogEntry $entry)
  {
    return MessageTable::getInstance()->find($entry->getMessage());
  }
  
  protected function getFiles(Message $message)
  {
    $site_url = sfConfig::get('app_site_url');
    
    $files = array();
    foreach($message->getFiles() as $file)
      $files[] = '<a href="'.$site_url.'/uploads/'.MessageFile::FILE_PATH.'/'.$file->getFile().'">'.$file->getFile().'</a>';

    $files = implode(', ', $files);
    
    return $files;
  }
}
