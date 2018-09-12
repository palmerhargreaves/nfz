<h1>Статистика по зимней акции</h1>

<?php if(isset($result)): ?>
<div class="alert alert-info">
  <strong>Дилеров с активированной акцией:</strong> <?php echo $result['total']; ?>
</div>

<a href='<?php echo url_for('winter_service_add'); ?>'>Добавить</a>
<table class="table table-bordered table-striped " cellspacing="0">
	<thead>
		<tr>
			<th >№</th>
			<th >Дилер</th>
			<th >Дата начала</th>
			<th >Дата окончания</th>
			
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
			<td class="span3"><span><?php echo $item->getStartDate() ?></span></td>
			<td class="span3"><span><?php echo $item->getEndDate() ?></span></td>
			
		</tr>
	<?php 
		}
	?>
</table>
	
<?php endif; ?>
