<table class="table table-hover table-condensed table-bordered table-striped">
    <thead>
        <tr>
          <th style='width: 1%;'>#</th>
          <th>Дилер</th>
          <th>Активность</th>
          <th>Дата блокировки</th>
          <th></th>
        </tr>
    </thead>

    <tbody>
    <?php 
    	  $ind = 1;
        foreach($items as $item):
    ?>
        <tr>
        	<td><?php echo $ind++; ?></td>
    			<td><?php echo sprintf('[%s] %s', $item->getDealer()->getNumber(), $item->getDealer()->getName()); ?></td>
    			<td><?php echo sprintf('[%s] %s', $item->getActivity()->getId(), $item->getActivity()->getName()); ?></td>
    			<td><?php echo $item->getCreatedAt(); ?></td>
    			<td><a href='javascript:;' class='btn-unblock-activity' data-id='<?php echo $item->getId(); ?>'><img src='/images/delete-icon.png' title='Удалить' /></a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>