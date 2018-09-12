<?php if(!$item): ?>
  <button class="btn btn-block-activity">Заблокировать</button>
<?php else: ?>
  <button class="btn btn-unblock-activity" data-id="<?php echo $item->getId(); ?>">Разблокировать</button>
<?php endif; ?>