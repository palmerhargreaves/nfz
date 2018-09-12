<table class="table table-hover table-condensed table-bordered table-striped table-export-dealers-stats-data">
	<thead>
		<tr>
			<th style="width: 30%;">Дилеры</th>
			<!--<th>Процент выполнения</th>-->
			<th>Сроки выполнения</th>
		</tr>
	</thead>

	<?php
		$statistic->buildDealerStats();
		$stats = $statistic->getDealerStats();
		//$fields = ActivityExtendedStatisticFieldsTable::getInstance()->createQuery()->where('activity_id = ?', array($activity))->orderBy('order ASC')->execute();
	?>
	<tbody>
		<?php foreach($stats as $dealerId => $item):
			$fieldsConcepts = ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->select('concept_id')->where('dealer_id = ? and concept_id != ?', array($dealerId, $activity))->groupBy('concept_id')->execute();
		?>
		<tr id="dealer-concept-cetrificate-<?php echo $dealerId; ?>">
			<td><?php echo sprintf('[%s] %s', $item['dealerNumber'], $item['dealerName']); ?></td>
			<!--<td><?php echo $item['percentOfComplete']."%"; ?></td>-->
			<td>
				<ul style="float: left;">
				<?php
					foreach($fieldsConcepts as $fieldConcept):
						$date = $fieldConcept->getConcept()->getAgreementModelSettings()->getCertificateDateTo();
						if(!empty($date)): ?>
							<li style="list-style-type: decimal;">
								<span class="dealer-certificate-item-<?php echo $fieldConcept->getConcept()->getId(); ?>" style=""><?php echo sprintf("Service Clinic (до): %s", date('d-m-Y', strtotime($fieldConcept->getConcept()->getAgreementModelSettings()->getCertificateDateTo()))); ?></span>
								<span class="dealer-certificate-item-<?php echo $fieldConcept->getConcept()->getId(); ?>" style=""> ( <img style="cursor: pointer;" class="on-delete-dealer-concept-certificate" data-id="<?php echo $fieldConcept->getConcept()->getId(); ?>" src="/images/delete-icon.png" title="Удалить" /> ) </span>
				 			</li>
				<?php
						endif;
					endforeach;
				?>
				</ul>

				<img src="/images/plus-icon.png" style="cursor: pointer;" class="on-add-new-concept pull-right tip" title="Добавить новый срок выполнения"
					 data-dealer-id="<?php echo $dealerId; ?>"
					 data-activity-id="<?php echo $activity; ?>" />
			</td>
		</tr>
	<?php 
		endforeach; 
	?>
	</tbody>

</table>

<script>

	$(function() {
		var table = $('.table-export-dealers-stats-data').dataTable({
			"bJQueryUI": false,
			"bAutoWidth": false,
			"bPaginate": true,
			"bLengthChange": false,
			"bInfo" : false,
			"bDestroy": true,
			"iDisplayLength" : 100,
			"sPaginationType": "full_numbers",
			//"sDom": '<"datatable-header"flp>t<"datatable-footer"ip>',
			"oLanguage": {
				"sSearch": "<span>Фильтр:</span> _INPUT_",
				"sLengthMenu": "<span>Отоброжать по:</span> _MENU_",
				"oPaginate": { "sFirst": "Начало", "sLast": "Посл", "sNext": ">", "sPrevious": "<" }
			},
			"aoColumnDefs": [
				{ "bSortable": false, "aTargets": [[1]] }
			]
		});
	});
</script>