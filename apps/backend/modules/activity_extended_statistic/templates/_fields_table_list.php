<?php if(count($fields) > 0): 
	$sections = array('fields' => array());

	foreach($fields as $field): 
		if(!array_key_exists($field->getParentId(), $sections))
			$sections[$field->getParentId()]['section'] = $field->getSection()->getHeader();

		$sections[$field->getParentId()]['fields'][] = $field;
	endforeach;

	foreach($sections as $key => $section):

	endforeach;
?>
<ul class="nav nav-list">
  <li class="nav-header">Список полей</li>
</ul>

<table class="table table-hover table-bordered table-striped">
	<thead>
		<tr>
		  <th style='width: 1%;'>#</th>
		  <th>Название</th>
		  <th>Описание</th>
		  <th>Тип поля</th>
		  <th>Значение</th>
		  <th>Обязательно для заполнения</th>
		  <th style='width: 10px;'></th>
		</tr>
	</thead>

	<tbody>
	<?php 
		$ind = 1;
		foreach($sections as $key => $section):
		?>
		<tr><td colspan='6'><strong><?php echo $section['section']; ?></strong></td></tr>
		<?php	
			foreach($section['fields'] as $field): 
		?>
			<tr>
				<td><?php echo $ind++; ?></td>
				<td><?php echo $field->getHeader(); ?></td>
				<td><?php echo $field->getDescription(); ?></td>
				<td><?php echo $field->getFieldType(); ?></td>
				<td><?php echo $field->getRawValue()->getFieldValue(); ?></td>
				<td><input type="checkbox" class="ch-required-field" <?php echo $field->getRequired() ? "checked" : ""; ?> data-field-id="<?php echo $field->getId(); ?>" title="Обязательное поле для заполнения" /></td>
				<td><a href='javascript:;' class='on-delete-field' data-is-calc='<?php echo $isCalcField ? 1 : 0; ?>' data-id='<?php echo $field->getId(); ?>'><img src='/images/delete-icon.png' title='Удалить' /></a></td>
			</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>