<?php

/**
 * MailMessageTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class MailMessageTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object MailMessageTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('MailMessage');
    }

    public function getSpooledMessages()
    {
        return $this->createQuery('m')
                        ->orderBy('m.priority');
    }
}