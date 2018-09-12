<?php 
	include_partial('activity_data', array('sections' => $sections, 
												'fields' => $fields, 
												'certificateItems' => $certificateItems, 
												'mailDealerList' => $mailDealerList, 
												'statistic' => $statistic, 
												'activity' => $activity));

	include_partial('concept_add', array('activity' => $activity));
?>
