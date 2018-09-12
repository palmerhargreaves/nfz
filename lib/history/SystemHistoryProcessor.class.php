<?php

/**
 * System history processor
 *
 * @author Сергей
 */
class SystemHistoryProcessor implements HistoryProcessor
{
    public function getSourceUri(LogEntry $entry)
    {
        switch ($entry->getObjectType()) {
            case 'activity':
                return '@activity_index?activity=' . $entry->getObjectId();
            case 'activity_file':
                $file = ActivityFileTable::getInstance()->find($entry->getObjectId());
                return $file ? '@activity_index?activity=' . $file->getActivityId() : false;
            case 'activity_task':
                $file = ActivityTaskTable::getInstance()->find($entry->getObjectId());
                return $file ? '@activity_index?activity=' . $file->getActivityId() : false;
            case 'budget':
                // TODO: сделать обработку для менеджера
                return '@homepage';
            case 'ask':
                return '@homepage#ask/' . $entry->getDealerId() . '/' . $entry->getMessageId();
        }

        return false;
    }

    public function getModelNumber(LogEntry $entry)
    {
        return 0;
    }
}
