<?php
echo 
  $dealer->getRawValue()->getName(), 
  ' (', substr(strval($dealer->getNumber()), -3), ') - ', 
  $activity->getRawValue()->getName(), ' - ',
  $model->isConcept() ? 'Концепция согласована' : 'Макет "'.$model->getRawValue()->getName().'" согласован';