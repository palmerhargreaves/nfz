<?php include_partial('activity/activities', array('activities' => $activities, 'year' => $year, 'models' => $models,
		'title' => 'Перечень активностей, влияющих на получение бонуса:', 
		'description' => 'В '.date('Y').' году одним из критериев для получения бонуса является выполнение 3 обязательных активностей в квартал.
		<br />Собственные дилерские активности в этот перечень не входят.')); ?>