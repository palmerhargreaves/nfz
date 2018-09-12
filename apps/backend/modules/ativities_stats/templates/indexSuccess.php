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
				<th>Действие</th>
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
					<td><?php //echo $item->getDealers()->count(); ?></td>
					<td><input type="button" class="export-activity-data" data-id="<?php echo $item->getId(); ?>" value="Выгрузить" /> </td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">

</script>