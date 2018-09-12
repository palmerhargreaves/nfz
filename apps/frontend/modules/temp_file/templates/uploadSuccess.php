<?php
if($success) 
  echo 'success,', $file->getId(), ',', $file->getFile();
else
  echo 'error';
?>