<h1>Статистика по загрузке файлов</h1>

<?php if(isset($result)): ?>
<div class="alert alert-info">
  <strong>Всего загрузок:</strong> <?php echo count($result); ?>
</div>

<table class="table table-bordered table-striped " cellspacing="0">
<thead>
	<tr>
		<th >№</th>
		<th >Дилер</th>
		<th >Файл</th>
	</tr>
	
	<?php
		$i = 1;
		foreach($result as $item) {
			$dealer = DealerTable::getInstance()->findOneByNumber('93500'.$item->getDealerIndex());
	?>
		<tr>
			<td class="span1"><?php echo $i++; ?></td>
			<td class="span3"><?php echo sprintf("%s (93500%s)", $dealer->getName(), $item->getDealerIndex()); ?></td>
			<td class="span4"><?php echo $item->getFileName(); ?></td>
		</tr>
	<?php
		}
	?>
</table>

<?php endif; ?>



