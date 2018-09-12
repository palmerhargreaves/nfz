<?php

/**
 * Description of FileNameHelper
 *
 * @author Сергей
 */
class FileNameHelper
{
    protected $path;
    protected $info;
    protected $known_extensions;
    protected $default_extension;

    function __construct($path, $known_extensions = null, $default_extension = 'unknow')
    {
        $this->path = $path;
        $this->info = pathinfo($path);
        $exploded_name = explode('.', $this->info['filename']);

        if (count($exploded_name) > 1) {
            $this->info['extension2'] = array_pop($exploded_name);
            $this->info['filename'] = implode('.', $exploded_name);
        }
        $this->known_extensions = $known_extensions ?: array('pdf', 'doc', 'fla', 'swf', 'zip', 'tif', 'rar');
        $this->default_extension = $default_extension;
    }

    function getPath()
    {
        return $this->path;
    }

    function getExtension()
    {
        return isset($this->info['extension']) ? $this->info['extension'] : '';
    }

    function getKnownExtensionIf()
    {
        $ext = $this->getKnownExtension();
        return $ext == $this->default_extension ? $this->getExtension() : $ext;
    }

    /**
     * Returns an extension from list of known ones.
     * If the file extension is not known the returns a default value.
     *
     * @return string
     */
    function getKnownExtension()
    {
        if (isset($this->info['extension2']) && in_array($this->info['extension2'], $this->known_extensions))
            return $this->info['extension2'];

        return in_array($this->getExtension(), $this->known_extensions)
            ? $this->getExtension()
            : $this->default_extension;
    }

    function getSize()
    {
        return file_exists($this->path) ? filesize($this->path) : 0;
    }

    function getSmartSize()
    {
        return F::getSmartSize($this->getSize());
    }
}
