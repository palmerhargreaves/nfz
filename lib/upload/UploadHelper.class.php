<?php

/**
 * Description of UploadHelper
 *
 * @author Сергей
 */
class UploadHelper extends Doctrine_Record_Listener
{
    protected $upload_path;
    protected $field_name;

    function __construct($field_name, $upload_path)
    {
        $this->field_name = $field_name;
        $this->upload_path = $upload_path;
    }

    function postDelete(Doctrine_Event $event)
    {
        $record = $event->getInvoker();
        $this->deleteFile($record, $record->get($this->field_name));
    }

    public function preInsert(Doctrine_Event $event)
    {
        $record = $event->getInvoker();
        $this->setupFile($record, $record->get($this->field_name));
    }

    function preUpdate(Doctrine_Event $event)
    {
        $record = $event->getInvoker();
        $file = $record->get($this->field_name);

        $this->setupFile($record, $file);

        $old = $record->getTable()->find($record->getId(), Doctrine_Core::HYDRATE_ARRAY);
        if (!$old)
            return;

        $old_file = $old[$this->field_name];
        if ($old_file && $old_file != $file)
            $this->deleteFile($record, $old_file);
    }

    protected function deleteFile($record, $file)
    {
        if ($file) {
            $reflection = new ReflectionObject($record);
            $method = 'delete' . ucfirst($this->field_name);
            if ($reflection->hasMethod($method))
                $record->$method($this->getUploadPath(), $file);

            @unlink($this->getUploadPath() . '/' . $file);
        }
    }

    protected function setupFile($record, $file)
    {
        if ($file) {
            $reflection = new ReflectionObject($record);
            $method = 'setup' . ucfirst($this->field_name);
            if ($reflection->hasMethod($method))
                $record->$method($this->getUploadPath(), $file);
        }
    }

    protected function getUploadPath()
    {
        return sfConfig::get('sf_upload_dir') . '/' . $this->upload_path;
    }
}
