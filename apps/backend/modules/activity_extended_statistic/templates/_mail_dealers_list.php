<table class="table table-hover table-condensed table-bordered table-striped table-mail-dealers-list">
	<thead>
		<tr>
		  <th style='width: 1%;'>#</th>
		  <th>Дилер</th>
		  <th>Дата</th>
		  <th style='width: 10px;'></th>
		  <th style='width: 1%;'></th>
		</tr>
	</thead>

	<tbody>
	<?php foreach($items as $item): ?>
		<tr>
			<td><input type='checkbox' class='ch-dealer-mail-item' data-id='<?php echo $item->getId() ;?>' /></td>
			<td><?php echo sprintf('[%s] %s', $item->getDealer()->getNumber(), $item->getDealer()->getName()); ?></td>
			<td><input type='text' class='mail-dealer-date' value='<?php echo $item->getDateTo(); ?>' data-id='<?php echo $item->getId(); ?>'></td>
			<td><a href='javascript:;'><img class='delete-mail-dealer-from-list' src='/images/delete-icon.png' title='Удалить' data-id='<?php echo $item->getId(); ?>' /></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<script>
	$(function() {
		$('input.mail-dealer-date').datepicker({ dateFormat: "dd-mm-yy" });
	});	
</script>