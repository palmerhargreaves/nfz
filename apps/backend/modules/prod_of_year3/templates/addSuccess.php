<h1>Добавить акционные данные</h1>

<form id='frmAddData' action='<?php echo url_for('prod_of_year_3_post_data'); ?>' method='post'>
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
		
		</table>

		<input type='submit' class='btn' style='float: right; margin-right: 10px;' value='Добавить'>
	</div>	
</form>

<script>
$(function(){
});
</script>