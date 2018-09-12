<table class="table table-hover table-bordered table-striped table-models-reports-stats">
	<thead>
	<tr>
		<th style='width: 1%;'>#</th>
		<th style="width: 160px;">Дата выгрузки</th>
		<th style="width: 260px;">Период статистики</th>
		<th style="width: 100px;">Всего заявок</th>
		<th style="width: 350px;">По статусам</th>
		<th style="text-align: right;">Действия</th>
	</tr>
	</thead>

	<tbody>
	<?php
		$ind = 1;
		foreach($items as $item):
            $models_comments_count = $item->getCommentModelsCount();
            $models_comments_specialist_count = $item->getCommentBySpecialistModelsCount();
            $models_total_sended = $item->getSendedModelsCount();
            $models_total_completed = $item->getCompletedModelsCount();
	?>
		<tr class="tr-report-stats-<?php echo $item->getId(); ?>">
			<td><input type="checkbox" data-id="<?php echo $item->getId(); ?>" class="ch-report-stats-item" /></td>
			<td><?php echo $item->getCreatedAt(); ?></td>
			<td><?php echo sprintf('%s / %s', date('d-m-Y', strtotime($item->getPeriodFromDate())), date('d-m-Y', strtotime($item->getPeriodToDate()))); ?></td>
			<td style="text-align: center;"><?php echo ($models_comments_count + $models_comments_specialist_count + $models_total_sended + $models_total_completed); ?></td>
			<td>
				Прокомментировано макетов: <span style="float: right;" class="label label-info"><?php echo $models_comments_count; ?></span><br />
                Прокомментировано макетов специалистом: <span style="float: right;" class="label label-info"><?php echo $models_comments_specialist_count; ?></span><br />
				Количество отправленных заявок: <span style="float: right;" class="label label-warning"><?php echo $models_total_sended; ?></span><br/>
				Количество согласованных отчетов: <span style="float: right;" class="label label-success"><?php echo $models_total_completed; ?></span>
			</td>
			<td><img class="action-delete-report-stats" data-id="<?php echo $item->getId(); ?>"
					 style="cursor: pointer; float: right;"
					 src="/images/delete-icon.png" title="Удалить статистику" /></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
