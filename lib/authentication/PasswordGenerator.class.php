<?php

/**
 * Description of PasswordGenerator
 *
 * @author Сергей
 */
class PasswordGenerator
{
  protected $len;
  protected $alphabet;
  protected $alphabet_length;
  
  function __construct($len = 10)
  {
    $this->len = $len;
    $this->alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';
    $this->alphabet_length = strlen($this->alphabet);
  }
  
  function generate()
  {
    $password = '';
    for($n = 0; $n < $this->len; $n ++)
      $password .= $this->alphabet[mt_rand(0, $this->alphabet_length - 1)];
    
    return $password;
  }
}
