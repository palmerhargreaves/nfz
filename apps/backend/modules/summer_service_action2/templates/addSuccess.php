<h1>Добавить акционные данные</h1>

<form id='frmAddData' action='<?php echo url_for('summer_service_post_data'); ?>' method='post'>
	<div style="display: block; width: 35%">
		<table class="table table-bordered table-striped " cellspacing="0" >
			<tr>
				<td class="span3">Дилер</td>
				<td class="span3">
					<select name='sb_dealer'>
					<?php
						foreach($dealers as $dealer) {
							echo '<option value="'.$dealer->getId().'">'.$dealer->getName().'</option>';
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
	$('#frmAddData input.date').datepicker({ dateFormat: "dd.mm.yy" });

	$('#frmAddData').validationEngine();
});
</script>