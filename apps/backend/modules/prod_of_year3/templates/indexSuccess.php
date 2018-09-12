<h1>Статистика по акции</h1>

<?php if(isset($result)): ?>
<div class="alert alert-info">
  <strong>Дилеров с активированной акцией:</strong> <?php echo $result['total']; ?>
</div>

<a href='<?php echo url_for('prod_of_year_3_add'); ?>'>Добавить</a>
<table class="table table-bordered table-striped " cellspacing="0">
	<thead>
		<tr>
			<th >№</th>
			<th >Дилер</th>
			<th>Дата регистрации</th>
			<th style='width: 30px;'>Действия</th>
		</tr>
	</thead>
	
	<?php
		foreach($result['items'] as $item) {
			//$dealer = $item->getDealer();
			
			$dealer = $item->getDealer();
			$user = $item->getUser();
	?>
		<tr>
			<td class="span3"><?php echo $item->getId() . "-". $dealer->getNumber(); ?></td>
			<td class="span4"><?php echo sprintf("%s (%s)", $dealer->getName(), $user->getName()); ?></td>
			<td class="span3"><span><?php echo $item->getCreatedAt() ?></span></td>
			<td ><img src='/images/delete-icon.png' title='Удалить' data-id='<?php echo $item->getId(); ?>' style="cursor:pointer;" class="delete-prod-item"></td>
		</tr>
	<?php 
		}
	?>
</table>
	
<?php endif; ?>

<script>
	$(function(){
		$(document).on('click', '.delete-prod-item', function() {
			if(confirm('Удалить ?')) {
				$.post('<?php echo url_for('prod_of_year_3_delete'); ?>', { id : $(this).data('id') }, function() {
					window.location.href = '<?php echo url_for('prod_of_year_3'); ?>';
				});
			}
		});
	});
</script>