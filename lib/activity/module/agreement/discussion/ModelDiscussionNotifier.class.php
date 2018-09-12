<?php

/**
 * Description of ModelDiscussionNotifier
 *
 * @author Сергей
 */
class ModelDiscussionNotifier extends BaseDiscussionNotifier
{
  function __construct()
  {
    parent::__construct('model_message', true);
  }
  
  protected function getMessage(User $user, array $entries)
  {
    return new AgreementModelDiscussionMail($user, $entries);
  }
  
  protected function getManagersQueryToNotify()
  {
    return parent::getManagersQueryToNotify()->andWhere('model_discussion_notification=?', true);
  }
}
