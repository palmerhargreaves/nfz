<h2>Статистика по скачиванию материалов</h2>

<?php if(isset($materials)): ?>

<div style='display: block; margin-bottom: 10px; padding: 5px;'>
	<h3>Фильтр</h3>

	<form id='frmFilterData' action='<?php echo url_for('material/downloads'); ?>' method='post'>
		<div style="display: block; width: 35%">
			<table class="table table-bordered table-striped " cellspacing="0" >
				<tr>
					<td class="span2">Дата с</td>
					<td class="span4">
						<input type='text' name='start_date' class='date' value='<?php echo $startDateFilter; ?>' >
					</td>
				</tr>

				<tr>
					<td class="span2">Дата до</td>
					<td class="span4">
						<input type='text' name='end_date' class='date' value='<?php echo $endDateFilter; ?>' >
					</td>
				</tr>

				<tr>
					<td class="span4" colspan='2'>
						<input type='button' class='btn' style='float: right; margin-right: 10px;' value='Очистить' data-url='<?php echo url_for('material_clear_download_filters'); ?>'>
						<input type='submit' class='btn' style='float: right; margin-right: 10px;' value='Фильтр'>	
					</td>					
				</tr>
			</table>
		</div>
	</form>
</div>

<div style='display: block; margin-bottom: 10px; padding: 5px;'>
	<h3>Список</h3>

	<table class="table table-striped table-bordered table-checks table-downloads-materails" cellspacing="0">
		<thead>
			<tr>
				<th >№ Активности</th>
				<th >Название активности</th>
				<th >Название материала</th>
				<th >Формат файла</th>
				<th >Количество скачиваний</th>
			</tr>
		</thead>
		
		<tbody>
		<?php
			foreach($materials as $item) {
				$matSource = MaterialSourceTable::getInstance()->find($item->getMaterialId());
				if(!$matSource)
					continue;

				$material = $matSource->getMaterial();
				$activity = $material->getActivity();
		?>
			<tr>
				<td class="span3"><?php echo $activity->getId(); ?></td>
				<td class="span3"><?php echo $activity->getName(); ?></td>
				<td class="span4"><?php echo $material->getName(); ?></td>
				<td class="span2">
				<?php 
					$ext = pathinfo($matSource->getFile(), PATHINFO_EXTENSION);
					echo $ext; ?>
				</td>
				<td class="span1">
					<span>
					<?php 
						$query = MaterialDownloadsTable::getInstance()->createQuery()->where('material_id = ?', $matSource->getId());

						if(!empty($startDateFilter))
					      $query->andWhere('created_at >= ?', D::toDb($startDateFilter));

					    if(!empty($endDateFilter))
					      $query->andWhere('created_at <= ?', D::toDb($endDateFilter));

						echo $query->count() ?>
					</span>
				</td>
			</tr>
		<?php
			}
		?>
		</tbody>
	</table>
</div>
	
<?php endif; ?>

<script>
$(function() {
	$('#frmFilterData input.date').datepicker({ dateFormat: "yy-mm-dd" });

	var table = $('.table-downloads-materails').dataTable({
							"bJQueryUI": false,
							"bAutoWidth": false,
							"bPaginate": true,
					        "bLengthChange": false,
					        "bInfo" : false,
					        "bDestroy": true,
					        "iDisplayLength" : 25,
							"sPaginationType": "full_numbers",
							"sDom": '<"datatable-header"flp>t<"datatable-footer"ip>',
							"oLanguage": {
								"sSearch": "<span>Фильтр:</span> _INPUT_",
								"sLengthMenu": "<span>Отоброжать по:</span> _MENU_",
								"oPaginate": { "sFirst": "Начало", "sLast": "Посл", "sNext": ">", "sPrevious": "<" }
							},
							"aoColumnDefs": [
						      { "bSortable": false, "aTargets": [] }
						    ]
					    });
    
	table.fnSort([ [ 0,'desc' ]]);

	$('input[type=button]').click(function(e) {
		e.stopPropagation();

		$('#frmFilterData').attr('action', $(this).data('url')).submit();
	});
});
</script>