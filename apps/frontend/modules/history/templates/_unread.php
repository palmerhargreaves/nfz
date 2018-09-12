<?php use_helper('Text'); ?>
<div id="user-messages">
        <div class="num-bg"></div>
        <div class="num"><?php echo $count ?></div>
        <div id="user-messages-menu">

<?php if($count > 0): ?>
                <div class="items">
  <?php foreach($last_history as $n => $entry): ?>
                  <a href="<?php echo url_for('@history_entry?id='.$entry->getId()) ?>" class="item<?php if($n == $count - 1) echo ' last' ?>"><?php echo truncate_text(strip_tags($entry->getDescription())) ?></a>
  <?php endforeach; ?>
                </div>
<?php else: ?>
<?php endif; ?>
                <div class="messages-view-all" onclick="location.href=$(this).data('url')" data-url="<?php echo url_for('@history') ?>">Посмотреть все</div>
        </div>
</div>
