<?php

/**
 * Description of AgreementModelAcceptedMail
 *
 * @author Сергей
 */
class HistoryMail extends TemplatedMail
{
    protected $params = array();

    function setParams($params)
    {
        $this->params = $params;
    }

    protected function getHistoryUrl(LogEntry $entry, $absolute = true)
    {
        return sfContext::getInstance()->getController()->genUrl('@history_entry?id=' . $entry->getId(), $absolute);
    }
}
