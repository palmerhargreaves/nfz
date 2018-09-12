<strong style='font-size: 20px;'><?php echo ActivityInfoFields::getNameById($fieldId); ?></strong>
<hr style='margin: 10px 0px;'/>
<?php foreach($items as $item): ?>
	<textarea cols="2" style="width: 450px;" name="field_data<?php echo $item->getFieldId(); ?>[]" 
						data-activity-id="<?php echo $item->getActivityId(); ?>" 
						data-field-id="<?php echo $item->getFieldId(); ?>"
						data-id="<?php echo $item->getId(); ?>"><?php echo $item->getDescription(); ?></textarea>
	<img class='action-field-data-delete' src='/images/delete-icon.png' title='Удалить' style='cursor: pointer;' data-id='<?php echo $item->getId(); ?>' data-field-id='<?php echo $fieldId; ?>' />
<?php endforeach; ?>

<a href='#' class='btn action-activity-config-info-params-add-field' data-field-id="<?php echo $fieldId; ?>" data-activity-id="<?php echo $activityId; ?>" style="text-align: center;">Добавить поле</a>