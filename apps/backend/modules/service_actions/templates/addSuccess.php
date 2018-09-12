<h3>Добавить акционные данные</h3>

<form id='frmAddData' action='<?php echo url_for('service_post_data'); ?>' method='post'>
	<div style="display: block; width: 35%">
		<table class="table table-bordered table-striped " cellspacing="0" >
			<tr>
				<td class="span3">Сервисная Акция</td>
				<td class="span3">
					<select id='sb_service' name='sb_service' class='validate[required]' data-url='<?php echo url_for('service_dealers_list'); ?>'>
						<option value=''>Выберите сервисную акцию ...</option>
					<?php
						foreach($serviceActions as $service) {
							echo '<option value="'.$service->getId().'">'.$service->getHeader().'</option>';
						}
					?>
					</select>
				</td>
			</tr>

			<tr>
				<td class="span3">Дилер</td>
				<td class="span3">
					<select id="sb_dealer" name='sb_dealer'>
					<?php
						foreach($dealers as $dealer) {
							$number = substr($dealer->getNumber(), -3);
							echo '<option value="'.$dealer->getId().'">'.sprintf("%s - %s", $number, $dealer->getName()).'</option>';
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="span4">Дата начала</td>
				<td class="span4">
					<input type='text' name='start_date' class='date validate[required]' >
				</td>
			</tr>

			<tr>
				<td class="span3">Дата окончания</td>
				<td class="span4">
					<input type='text' name='end_date' class='date validate[required]' >
				</td>
			</tr>
		</table>

		<input type='submit' class='btn' style='float: right; margin-right: 10px;' value='Добавить'>
	</div>
	
</form>

<script>
$(function(){
	$('#sb_service').change(function() {
		$.post($(this).data('url'),
				{
					id: $(this).val()
				},
				function(result) {
					$('#sb_dealer').empty().html(result);
				});	
	});

	$('#frmAddData input.date').datepicker({ dateFormat: "dd.mm.yy" });

	$('#frmAddData').validationEngine();
});
</script>