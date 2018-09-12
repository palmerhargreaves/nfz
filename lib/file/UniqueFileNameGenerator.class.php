<?php

/**
 * Unique file name generator
 */
class UniqueFileNameGenerator
{
    const max_underscores_percent = 80;

    protected $path;
    protected $len_name;

    function __construct($path, $len_name = 60)
    {
        $this->path = $path;
        $this->len_name = $len_name;
    }

    function generate($base_name)
    {
        $exp_name = $this->explodeName($base_name);
        $norm_name = $this->normalize($exp_name['name']);
        return $this->getUniqueName($norm_name, $exp_name['ext'], $exp_name['second_ext']);
    }

    function getPath()
    {
        return $this->path;
    }

    protected function getUniqueName($base_name, $ext, $second_ext)
    {
        $file_name = $base_name . $second_ext . $ext;
        while (file_exists($this->path . '/' . $file_name)) {
            $file_name = $base_name . mt_rand(0, 1000) . $second_ext . $ext;
        }
        return $file_name;
    }

    /**
     * Return array( 'name' => file_name, 'ext' => extension_with_dot )
     *
     * @return array
     */
    protected function explodeName($name)
    {
        $exploded = explode('.', $name);
        $ext = '';
        $second_ext = '';

        if (count($exploded) > 1)
            $ext = '.' . $this->normalize(array_pop($exploded));

        if (count($exploded) > 1) {
            $e = $this->normalize($exploded[count($exploded) - 1]);
            if (strlen($e) < 7) {
                $second_ext = '.' . $e;
                array_pop($exploded);
            }
        }

        return array(
            'name' => implode('.', $exploded),
            'ext' => $ext,
            'second_ext' => $second_ext
        );
    }

    protected function normalize($name)
    {
        $str = '';
        $name = mb_strtolower($this->toUtf8($name), 'UTF-8');
        for ($n = 0, $len = mb_strlen($name, 'UTF-8'); $n < $len; $n++) {
            $new_sym = $sym = mb_substr($name, $n, 1, 'UTF-8');
            if (!$this->isSymEnabled($sym)) {
                $new_sym = $this->symToTranslit($sym);
                if (!$new_sym)
                    $new_sym = '_';
            }
            $str .= $new_sym;
        }
        if ($this->isUnderScoresTooMuch($str))
            $str = strval(mt_rand(0, 100000));

        return substr($str, 0, $this->len_name);
    }

    protected function isUnderScoresTooMuch($str)
    {
        $count = substr_count($str, '_');
        return $count / strlen($str) * 100 > self::max_underscores_percent;
    }

    protected function toUtf8($name)
    {
        return mb_convert_encoding($name, 'UTF-8', 'UTF-8,CP1251,ASCII');
    }

    protected function isSymEnabled($sym)
    {
        $enabled = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return mb_strpos($enabled, $sym, 0, 'UTF-8') !== false;
    }

    protected function symToTranslit($sym)
    {
        static $translit = array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ы' => 'yi',
            'э' => 'ye',
            'ю' => 'yu',
            'я' => 'ya'
        );

        return isset($translit[$sym]) ? $translit[$sym] : false;
    }
}