<?php
function history_title($title)
{
  $exploded = explode('/', $title, 2);
  $title = '<strong>'.$exploded[0].'</strong>';
  if(isset($exploded[1]))
    $title .= ' / '.$exploded[1];
  
  return $title;
}