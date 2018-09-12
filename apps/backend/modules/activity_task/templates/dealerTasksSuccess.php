<div class="dealer-tasks">
<?php foreach($tasks as $task): ?>
  <div class="task">
  <?php foreach($task->getResults() as $result): ?>
    <?php if($result->getDone()): ?>
    <a href="<?php echo url_for("@cancel_dealer_tasks?task_id={$task->getId()}&dealer_id=$dealer_id") ?>"><img src="/images/ok-icon-active.png" alt="отменить" title="отменить"/></a>
    <?php else: ?>
    <a href="<?php echo url_for("@accept_dealer_tasks?task_id={$task->getId()}&dealer_id=$dealer_id") ?>"><img src="/images/ok-icon.png" alt="завершить" title="завершить"/></a>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php if($task->getResults()->count() == 0): ?>
    <a href="<?php echo url_for("@accept_dealer_tasks?task_id={$task->getId()}&dealer_id=$dealer_id") ?>"><img src="/images/ok-icon.png" alt="завершить" title="завершить"/></a>
  <?php endif; ?>
    &nbsp;
  <?php echo $task->getName() ?>
  </div>
<?php endforeach; ?>
</div>