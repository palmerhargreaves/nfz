<?php

/**
 * Notifies about new messages in discussions with dealers
 *
 * @author Сергей
 */
class DealerDiscussionNotifier extends BaseDiscussionNotifier
{
  function __construct()
  {
    parent::__construct('ask');
  }
  
  protected function getMessage(User $user, array $entries)
  {
    return new DealerDiscussionMail($user, $entries);
  }
  
  protected function getManagersQueryToNotify()
  {
    return parent::getManagersQueryToNotify()->andWhere('dealer_discussion_notification=?', true);
  }
}
