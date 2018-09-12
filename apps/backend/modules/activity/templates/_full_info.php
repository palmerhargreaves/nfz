<ul class="sf_admin_actions">
<?php 
	$data = $activity->getInfoData();
	if(count($data)): 
?>
  <li class="sf_admin_action_edit modal-activity-info-params"><a href="#" class="action-activity-config-info-params" data-id="<?php echo $activity->getId(); ?>">Редактировать</a></li>
<?php else: ?>
  <li class="sf_admin_action_new modal-activity-info-params"><a href="#" class="action-activity-config-info-params" data-id="<?php echo $activity->getId(); ?>">Добавить</a></li>
<?php endif; ?>
</ul>

