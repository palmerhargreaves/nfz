<?php

/**
 * Interface of a history processor
 *
 * @author Сергей
 */
interface HistoryProcessor
{
    /**
     * Returns source uri or false if source is not found
     *
     * @param LogEntry $entry
     * @return boolean|string
     */
    function getSourceUri(LogEntry $entry);

    /**
     * @param LogEntry $entry
     * @return number
     */
    function getModelNumber(LogEntry $entry);
}
