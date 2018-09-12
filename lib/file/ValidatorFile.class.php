<?php

/**
 * Description of ValidatorFile
 *
 * @author Сергей
 */
class ValidatorFile extends sfValidatorFile
{
    private $_check_for_ms_files = true;

    protected function configure($options = array(), $messages = array())
    {
        parent::configure($options, $messages);

        $this->addOption('mime_type_guessers', array(
            array('F', 'getFileMimeType'),
            array($this, 'guessFromFileinfo'),
            array($this, 'guessFromMimeContentType'),
            array($this, 'guessFromFileBinary'),
        ));
    }

    protected function checkMimeForExcel($mimeType)
    {
        if ($this->_check_for_ms_files) {
            return parent::checkMimeForExcel($mimeType); // TODO: Change the autogenerated stub
        }

        return false;
    }

    public function setCheckForMsFiles($check_for_ms_files = true) {
        $this->_check_for_ms_files = $check_for_ms_files;
    }
}
