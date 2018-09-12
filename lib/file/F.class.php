<?php

/**
 * File utils
 *
 * @author Сергей
 */
class F
{
    const APACHE_MIME_TYPES_URL = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
    static private $mime_types = null;

    /**
     *
     * @return array
     */
    static private function generateUpToDateMimeArray()
    {
        $types = array();
        foreach (explode("\n", file_get_contents(self::APACHE_MIME_TYPES_URL)) as $x)
            if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ($c = count($out[1])) > 1)
                for ($i = 1; $i < $c; $i++)
                    $types[$out[1][$i]] = $out[1][0];
        return $types;
    }

    static private function getAvailableMimeTypes()
    {
        if (self::$mime_types === null) {
            $cache_path = sfConfig::get('sf_cache_dir') . '/mime_types.php';
            if (!file_exists($cache_path))
                file_put_contents($cache_path, "<?php\n\$mime_types=" . var_export(self::generateUpToDateMimeArray(), true) . ';');

            include $cache_path;

            self::$mime_types = $mime_types;
        }
        return self::$mime_types;
    }

    static function getFileMimeType($path)
    {
        $info = pathinfo($path);
        if (!isset($info['extension']))
            return null;

        $extension = $info['extension'];
        $mime_types = self::getAvailableMimeTypes();

        //If server not have mime type, add custom mime type
        if (!array_key_exists($extension, $mime_types)) {
            $mime_types[$extension] = 'application/octet-stream';
        }

        return isset($mime_types[$extension]) ? $mime_types[$extension] : null;
    }

    static function getFiles($path)
    {
        $files = array();
        if (file_exists($path)) {
            foreach (scandir($path) as $file) {
                if (is_file($path . '/' . $file))
                    $files[] = $file;
            }
        }
        return $files;
    }

    static function getSmartSize($size, $decimals = 1)
    {
        static $units = array('Б', 'КБ', 'МБ', 'ГБ');

        foreach ($units as $n => $unit) {
            if ($size < 1024)
                return number_format($size, $n > 0 ? $decimals : 0, ',', ' ') . ' ' . $unit;

            $size /= 1024;
        }

        return number_format($size * 1024, $decimals, ',') . ' ' . end($units);
    }

    static function imageResize($image, $width)
    {
        $imagesExt = array('gif', 'png', 'jpeg', 'jpg');
        $fileInfo = pathinfo($image);

        if (!in_array($fileInfo['extension'], $imagesExt)) {
            return null;
        }

        $imageSize = getimagesize($image);

        switch ($imageSize[2]) {
            case 1:
                $imOrig = imagecreatefromgif($image);
                break;
            case 2:
                $imOrig = imagecreatefromjpeg($image);
                break;
            case 3:
                $imOrig = imagecreatefrompng($image);
                break;
            default:
                $imOrig = imagecreatefromjpeg($image);
                break;
        }

        $x = imagesx($imOrig);
        $y = imagesy($imOrig);
        $ratio = $x / $y;

        $scaleH = $width / $x;
        $scaleW = $width / $y;

        $woh = $imageSize[0];
        if ($woh < $width) {
            $aw = $x;
            $ah = $y;
        } else {
            $aw = $width;

            if ($y > $x) {
                //$ah = $width / $ratio;
                $ah = $scaleH * $y;
            } else {
                $ah = $y;
            }
        }

        $im = imagecreatetruecolor($aw, $ah);
        if (imagecopyresampled($im, $imOrig, 0, 0, 0, 0, $aw, $ah, $x, $y)) {
            $fileSaveToName = $fileInfo['filename'] . '_copy.jpg';
            $fileSaveTo = sfConfig::get('sf_root_dir') . '/www/uploads/' . AgreementModelReport::ADDITIONAL_FILE_PATH . '/' . $fileSaveToName;
            if (imagejpeg($im, $fileSaveTo)) {
                return array('file' => $fileSaveToName, 'width' => $aw, 'aw' => $aw, 'ah' => $ah);
            }
        }

        return null;
    }

    static function downloadFile($path, $fileName)
    {
        $maxFilesSize = 100 * 1024 * 1024;

        if (file_exists($path)) {
            $mimeType = F::getFileMimeType($path);
            if (is_null($mimeType)) {
                return '';
            } else {

                if(filesize($path) >= $maxFilesSize) {
                    return str_replace(sfConfig::get('app_www_path'), sfConfig::get('app_site_url'), $path);
                } else {
                    $disposition = 'attachment';
                    $inlineMimeTypes = array(
                        'image/jpeg',
                        'application/pdf',
                        'audio/mpeg',
                        'image/png',
                        'application/x-shockwave-flash',
                        'audio/wav',
                        'audio/mpeg',
                        'video/avi',
                        'video/msvideo',
                        'video/x-msvideo',
                        'video/avs-video');

                    if (in_array($mimeType, $inlineMimeTypes)) {
                        $disposition = 'inline';
                    }

                    header('Content-Description: File Transfer');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Content-Length: ' . filesize($path));
                    header("Content-Disposition: {$disposition}; filename={$fileName}");
                    header('Content-Type: ' . $mimeType);
                    header('Accept-Ranges: bytes');
                    header('Content-Transfer-Encoding: binary');

                    header('Cache-Control: private');
                    $fb = fopen($path, 'rb');

                    while (!feof($fb)) {
                        print @fread($fb, 10 * (1024 * 1024));

                        ob_flush();
                        flush();
                    }
                    fclose($fb);

                    return 'success';
                }
            }
        }

        return '';
    }

    public static function isImage($file) {
        $file_ext = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($file_ext, self::getImgExt())) {
            return true;
        }

        return false;
    }

    public static function getImgExt() {
        return array('png', 'gif', 'jpg', 'jpeg');
    }

    public static function copyExternalFileTo($from, $to) {
        $generator = new UniqueFileNameGenerator($to);
        $gen_file_name = $generator->generate(basename($from));

        copy($from, $to.'/'.$gen_file_name);

        return $gen_file_name;
    }
}
