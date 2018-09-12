<?php
echo 
  $dealer->getRawValue()->getName(), 
  ' (', substr(strval($dealer->getNumber()), -3), ') - ', 
  $activity->getRawValue()->getName(), ' - ',
  $model->isConcept() ? 'отчёт по концепции согласован' : 'отчёт "'.$model->getRawValue()->getName().'" согласован';