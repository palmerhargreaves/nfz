<h1>Велосипеды</h1>

<?php if(isset($result)): ?>
<div class="alert alert-info">
  <strong>Дилеров с активированной акцией:</strong> <?php echo count($result); ?>
</div>

<table class="table table-bordered table-striped " cellspacing="0">
<thead>
	<tr>
		<th >Дилер</th>
		<th >Список велосипедов</th>
		<th >Дата</th>
	</tr>
	
	<?php
		foreach($result as $key => $item) {
			$dealer = $item['data']['dealer'];
			$itemData = $item['data']['item'];


	?>
		<tr>
			<td class="span3"><?php echo sprintf("%s %s (%s)", $dealer->getName(), $dealer->getSurname(), $item['data']['dealerNumber']); ?></td>
			<td class="span6">
				<table class="table table-bordered table-striped " cellspacing="0">
					<thead>
						<tr>
							<td>Наименование</td>
                            <td>Артикул</td>
                            <td>НЕР. руб.</td>
                            <td>РРЦ. руб.</td>
                            <td>Количество</td>
						</tr>
					</thead>

					<tbody>
						<?php 
							$ftype = 0;
							foreach($item['data']['bikes'] as $type => $bikeItems):
								
								foreach($bikeItems as $bikeItem):
						?>
								<tr>
									<td><?php echo $bikeItem['name']; ?></td>
									<td><?php echo $bikeItem['article']; ?></td>
									<td><?php echo $bikeItem['nep']; ?></td>
									<td><?php echo $bikeItem['rrc']; ?></td>
									<td><?php echo $bikeItem['count']; ?></td>
								</tr>
						<?php 
								endforeach;
							endforeach; ?>
					</tbody>
				</table>
			</td>
			<td class="span3"><?php echo $itemData['date_of_order']; ?></td>
		</tr>
	<?php
		}
	?>
</table>
	
<?php endif; ?>
