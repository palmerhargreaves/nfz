<ul>
<?php foreach($fields as $field): ?>
	<li>
		<img src='/images/<?php echo $field->getImage(); ?>' />
		<a href='#' class='action-activity-load-field-data field-info-<?php echo $field->getId(); ?>' data-id='<?php echo $activityId; ?>' data-field-id='<?php echo $field->getId(); ?>'><?php echo $field->getHeader(); ?></a>
		<span style='float: right;'><?php echo $field->getFieldDataCount($activityId); ?></span>
	</li>
<?php endforeach; ?>
</ul>