<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<div class="well sidebar-nav">
				<ul class="nav nav-list">
					<li class="nav-header">Статистика по активностям</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="row-fluid">
		<table class="table table-hover table-bordered table-striped">
			<thead>
			<tr>
				<th style='width: 1%;'>№</th>
				<th>Активность</th>
				<th>Дилеров</th>
				<th style="width: 100px;">Действие</th>
			</tr>
			</thead>

			<tbody>
			<?php
				$ind = 1;
				foreach($activities as $item):
			?>
				<tr>
					<td><?php echo $item->getId(); ?></td>
					<td><?php echo $item->getName(); ?></td>
					<td><?php echo $item->getDealersStatsCount(); ?></td>
					<td><input type="button" class="export-activity-data" data-id="<?php echo $item->getId(); ?>" value="Выгрузить" /> </td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(document).on('click', '.export-activity-data', function() {
		var $bt = $(this), activityId = $bt.data('id');

		$bt.prop('disabled', true);
		$.post('<?php echo url_for('activity_stats_export_data'); ?>',
			{
				activityId : activityId
			},
			function(result) {
				$bt.prop('disabled', false);

				window.location.href = 'http://dm.vw-servicepool.ru/uploads/activity_stats.xls';
			});
	});
</script>