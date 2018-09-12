<table class="table table-hover table-bordered table-striped">
	<thead>
	<tr>
		<th style='width: 1%;'>№</th>
		<?php if($showHistory): ?>
			<th>№ Заявки</th>
			<th>Название</th>
		<?php endif; ?>
		<th>Описание</th>
		<th>Пользователь</th>
		<th>Дилер</th>
		<th>Дата</th>
	</tr>
	</thead>

	<tbody>
	<?php
		$ind = 1;
		foreach($items as $item):
	?>
		<tr>
			<td><?php echo $ind++; ?></td>
			<?php if($showHistory): ?>
				<td><?php echo $item->getObjectId(); ?></td>
				<td>
					<?php echo $item->getTitle(); ?>
					<?php if($showHistory): ?>
						<span class="badge badge-success action-show-model-logs" style="float: right; cursor: pointer;" title="Показать историю заявки" data-object-id="<?php echo $item->getObjectId(); ?>">История</span>
					<?php endif; ?>
				</td>
			<?php endif; ?>
			<td><?php echo $item->getDescription(); ?></td>
			<td><?php echo $item->getLogin(); ?></td>
			<td><?php echo sprintf('[%s] %s', $item->getDealer()->getNumber(), $item->getDealer()->getName()); ?></td>
			<td><?php echo $item->getCreatedAt(); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
