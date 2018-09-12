<?php

/**
 * Description of MailSpool
 *
 * @author Мишаня
 */
class MailSpool extends Swift_DoctrineSpool
{
    public function queueMessage(Swift_Mime_Message $message)
    {
        $object = new $this->model;

        if (!$object instanceof Doctrine_Record) {
            throw new InvalidArgumentException('The mailer message object must be a Doctrine_Record object.');
        }

        if (!$message->getCanSendMail()) {
            $object->setCanSend(false);
            $object->setModelId($message->getModelId());
        }

        $object->setMustDelete($message->getMustDelete());

        $object->{$this->column} = serialize($message);
        $object->save();

        $object->free(true);
    }

    /**
     * Sends messages using the given transport instance.
     *
     * @param Swift_Transport $transport A transport instance
     * @param string[] &$failedRecipients An array of failures by-reference
     *
     * @return int The number of sent emails
     */
    public function flushQueue(Swift_Transport $transport, &$failedRecipients = null)
    {
        $table = Doctrine_Core::getTable($this->model);
        $objects = $table->{$this->method}()/*->limit($this->getMessageLimit())*/->execute();

        if (!$transport->isStarted()) {
            $transport->start();
        }

        $objects_to_delete = array();

        $count = 0;
        $time = time();
        foreach ($objects as $object) {
            /*Check if we can send mail*/
            if (!$object->getCanSend()) {

                /*Check if we must delete mail to clean mails list*/
                if ($object->getMustDelete()) {
                    $objects_to_delete[] = $object;
                }

                continue;
            }

            $message = unserialize($object->{$this->column});
            $before = $count;

            try {
                $count += $transport->send($message, $failedRecipients);
            } catch (Exception $e) {
                // TODO: What to do with errors?
            }

            if ($count > $before) {
                $object->delete();
            } else {
                $object->setPriority($object->getPriority() + 1);
                $object->save();

                if ($object->getPriority() > 3)
                    $object->delete();
            }

            if ($count % 5 == 0)
                sleep(1);

            if ($this->getTimeLimit() && (time() - $time) >= $this->getTimeLimit()) {
                break;
            }
        }

        /**
         * Delete not sended mails
         */
        foreach ($objects_to_delete as $object) {
            $object->delete();
        }

        return $count;
    }

}
