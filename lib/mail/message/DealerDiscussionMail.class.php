<?php

/**
 * Notify mail about new messages in ask-chat
 *
 * @author Сергей
 */
class DealerDiscussionMail extends DiscussionMail
{
    function __construct(User $user, $entries, $email = '')
    {
        $site_url = sfConfig::get('app_site_url');

        $discussion_messages = array();

        foreach ($entries as $entry) {
            $sender = $entry->getUser();
            $message = $this->getMessage($entry);
            if (!$message)
                continue;

            $text = nl2br(strip_tags($message->getText()));

            $date = date('d.m.Y H:i', D::toUnix($message->created_at));

            $dealer = $entry->getDealer() ? $entry->getDealer()->getName() : '';

            $discussion_messages[] =
<<<TEXT
        <p>
          <small>{$dealer}</small>
          <br>
          <i>$date</i>
          <b>{$sender->selectName()}:</b>
          <br>
          {$text}
          <div>{$this->getFiles($message)}</div>
          <br>
          <a href="{$site_url}/history/{$entry->getId()}">перейти к сообщению</a>
        </p>
TEXT;
        }

        $discussion_messages = implode('', $discussion_messages);

        parent::__construct(
            empty($email) ? $user->getEmail() : $email,
            'global/mail_common',
            array(
                'user' => $user,
                'subject' => 'Новые сообщения',
                'text' =>
<<<TEXT
                            <p>Уважаемый пользователь!</p>
        <p>Поступили новые сообщения:</p>
        {$discussion_messages}
TEXT
            )
        );
    }
}
