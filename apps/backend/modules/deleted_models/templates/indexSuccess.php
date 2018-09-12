<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<div class="well sidebar-nav">
				<ul class="nav nav-list">
					<li class="nav-header">Список удаленных заявок - всего [<?php echo $totalDeletedItems; ?>]</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12">
			<div class="well sidebar-nav">
				<div class="alert alert-<?php echo count($items) > 0 ? "success" : "error"; ?> container-success" style="">
					<?php if(count($items) > 0): ?>
						Всего найдено удаленных макетов: <?php echo count($items) ;?>
					<?php else: ?>
						Ничего не найдено
					<?php endif; ?>
				</div>

				<form action="<?php echo url_for('@deleted_models'); ?>">
					<ul class="nav nav-list">
						<li class="nav-header">Фильтр по удаленным макетам</li>
						<li>
							Индекс макета:<br/>
							<input type="text" name="txtModelIndex" placeholder="Введите индекс макета" value="<?php echo isset($modelId) ? $modelId : ""; ?>" />
						</li>
						<li>
							Дилер:<br/>
							<select id="sbDealer" name="sbDealer">
								<option value="-1">Выберите дилера ...</option>
								<?php foreach($dealers as $dealer):
									$sel = isset($dealerFilter) && $dealerFilter == $dealer->getId() ? "selected" : "";
									?>
									<option value="<?php echo $dealer->getId(); ?>" <?php echo $sel; ?>><?php echo sprintf('[%s] %s', $dealer->getNumber(), $dealer->getName()); ?></option>
								<?php endforeach; ?>
							</select>
						</li>
						<li>Период:<br/>
							<input type="text" name="txtStartDateFilter" class="date input-medium" value="<?php echo isset($startDateFilter) ? $startDateFilter : ""; ?>" placeholder="от"/>
							-
							<input type="text" name="txtEndDateFilter" class="date input-medium" value="<?php echo isset($endDateFilter) ? $endDateFilter : ""; ?>" placeholder="до" />
						</li>
						<li>
							<label class="checkbox">
								<input type="checkbox" id="chShowAll" name="chShowAll" <?php echo isset($showAllFilter) && $showAllFilter == 1 ? "checked" : ""; ?> />Показать все
							</label>
						</li>
						<li>
							<input type="submit" id="btDoFilterData" class="btn" style="margin-top: 15px;" value="Фильтр" />
						</li>
					</ul>
				</form>
			</div>
		</div>
	</div>

	<div id="deleted-models-list" class="row-fluid">
		<?php include_partial('model_history_list', array('items' => $items, 'showHistory' => true)); ?>
	</div>
</div>

<div class="modal hide fade model-history-modal" id="model-history-modal" style="width: 700px; left: 45%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4>История по заявке</h4>
	</div>
	<div class="modal-body">
		<div class="modal-content-container" style="width: 100%; float:left;"></div>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
	</div>
</div>

<script type="text/javascript">
	$('input.date').datepicker({ dateFormat: "dd-mm-yy" });

	window.modelLogs = new ModelLogs({
		modal: '#model-history-modal',
		show_url: '<?php echo url_for('deleted_model_history'); ?>'
	}).start();
</script>