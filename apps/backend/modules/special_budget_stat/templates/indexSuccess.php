<h1>Статистика по акции</h1>

<?php if(isset($result)): ?>
<div class="alert alert-info">
  <strong>Дилеров с активированной акцией:</strong> <?php echo $result['total']; ?>
</div>

<table class="table table-bordered table-striped " cellspacing="0">
<thead>
	<tr>
		<th >№</th>
		<th >Дилер</th>
		<th >Бюджет на 1 квартал 2014</th>
		<th >Сумма</th>
		<th >Процент</th>
		<th >Дата активации</th>
	</tr>
	
	<?php
		$maxP = 100;
	
		foreach($result['items'] as $item) {
			$bud = $item->getSpecialBudgetQuater();
			$sum = $item->getSpecialBudgetSumm();
			
			$percent = ($sum / $bud) * 100;
			$percent = $percent - $maxP;
			
			$dealer = $item->getDealerUsers()->getFirst()->getDealer();
	?>
		<tr>
			<td class="span3"><?php echo $dealer->getNumber(); ?></td>
			<td class="span4"><?php echo sprintf("%s (%s %s)", $dealer->getName(), $item->getSurname(), $item->getName()); ?></td>
			<td class="span3"><span><?php echo number_format($bud, 0, '.', ' ') ?></span> руб.</td>
			<td class="span3"><span><?php echo number_format($sum, 0, '.', ' ') ?></span> руб.
			<td class="span3"><?php echo round($percent, 0)."%"; ?></td>
			<td class="span3"><?php echo $item->getSpecialBudgetDateOf(); ?></td>
		</tr>
	<?php
		}
	?>
</table>
	
<?php endif; ?>

<?php if(isset($result2)): ?>
<div class="alert alert-info">
  <strong>Дилеров с отклоненной акцией:</strong> <?php echo $result2['total'] ?>
</div>

<table class="table table-bordered table-striped " cellspacing="0">
<thead>
	<tr>
		<th >Дилер</th>
		<th >Дата отказа</th>
	</tr>
	
	<?php
		foreach($result2['items'] as $item) {
	?>
		<tr>
			<td class="span4"><?php echo sprintf("%s (%s %s)", $item->getDealerUsers()->getFirst()->getDealer()->getName(), $item->getSurname(), $item->getName()); ?></td>
			<td class="span3"><?php echo $item->getSpecialBudgetDateOf(); ?></td>
		</tr>
	<?php
		}
	?>
</table>

<?php endif; ?>

