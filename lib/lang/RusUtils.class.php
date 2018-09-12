<?php

/**
 * Description of RusUtils
 *
 * @author Сергей
 */
class RusUtils
{
  static function pluralDealerEnding($num)
  {
    return self::pluralEnding1($num, 'дилер', array('а', 'ов'));
  }
  
  static function pluralModelsEnding($num)
  {
    return self::pluralEnding1($num, 'макет', array('а', 'ов'));
  }
  
  static function pluralEnding1($num, $base, $endings)
  {
    $num = intval($num);
    
    $tens = substr(strval($num), -2);
    if($tens >= 10 && $tens <= 20)
      return $base.$endings[1];
    
    $identity = substr(strval($num), -1);
    
    if($identity == 1)
      return $base;
    if($identity >= 2 && $identity <= 4)
      return $base.$endings[0];
    
    return $base.$endings[1];
  }
}
