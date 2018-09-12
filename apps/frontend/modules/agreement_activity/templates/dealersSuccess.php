<?php echo $activity->getName(), '. ', $status, '.' ?>
<?php if(count($dealers) > 0): ?>
<ul>
  <?php foreach($dealers as $dealer): ?>
    <li><?php echo $dealer->getRawValue(); ?></li>
  <?php endforeach; ?>
</ul>
<?php else: ?>
<div>Дилеры отсутствуют</div>
<?php endif; ?>
