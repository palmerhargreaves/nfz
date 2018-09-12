<h1>Статистика по Сервисной акции</h1>

<?php if(isset($result)): ?>
<div class="alert alert-info">
  <strong>Дилеров с активированной акцией:</strong> <?php echo $result['total']; ?>
</div>

<table class="table table-bordered table-striped " cellspacing="0">
<thead>
	<tr>
		<th >№</th>
		<th >Дилер</th>
		<th >Дата начала</th>
		<th >Дата окончания</th>
		<th >Осталось дней</th>
	</tr>
	
	<?php
		foreach($result['items'] as $item) {
			$dealer = $item->getDealerUsers()->getFirst()->getDealer();
	?>
		<tr>
			<td class="span3"><?php echo $dealer->getNumber(); ?></td>
			<td class="span4"><?php echo sprintf("%s (%s %s)", $dealer->getName(), $item->getSurname(), $item->getName()); ?></td>
			<td class="span3"><span><?php echo $item->getSummerActionStartDate() ?></span></td>
			<td class="span3"><span><?php echo $item->getSummerActionEndDate() ?></span></td>
			<td class="span3"><span><?php echo $item->getElapsedSummerServiceAction() ?> дн.</span></td>
		</tr>
	<?php
		}
	?>
</table>
	
<?php endif; ?>
