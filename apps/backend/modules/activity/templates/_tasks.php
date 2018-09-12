<ul class="sf_admin_actions">
<?php
  $tasks = ActivityTaskTable::getInstance()->createQuery()->where('activity_id = ?', $activity->getId())->orderBy('position ASC')->execute();
?>
<?php //foreach($activity->getTasks() as $n => $task):
foreach($tasks as $n => $task): ?>
  <li>
    <a href="<?php echo url_for('activity_task/edit/?id='.$task->getId()) ?>"><?php echo $task->getName() ?></a>
    <ul>
      <li class="sf_admin_action_delete">
        <a href="<?php echo url_for('activity_task/delete/?id='.$task->getId()) ?>" onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
      </li>
    </ul>
  </li>
<?php endforeach; ?>
  <li class="sf_admin_action_new"><a href="<?php echo url_for('activity_task/new?activity_id='.$activity->getId()) ?>">Добавить</a></li>
<?php if(count($activity->getTasks()) > 0): ?>
  <li class="sf_admin_action_edit"><a href="#" onclick="window.tasks_acceptor.showWithActivity($(this).data('activity-id'), $(this).data('activity-name')); return false;" data-activity-id="<?php echo $activity->getId() ?>" data-activity-name="<?php echo $activity->getName() ?>">Отметить выполнение</a></li>
<?php if(getenv('REMOTE_ADDR') == '46.175.166.61'): ?>
  <li class="sf_admin_action_edit"><a href="#" onclick="window.tasks_orders.showWithActivity($(this).data('activity-id'), $(this).data('activity-name')); return false;" data-activity-id="<?php echo $activity->getId() ?>" data-activity-name="<?php echo $activity->getName() ?>">Изменить порядок</a></li>
<?php endif; ?>
<?php endif; ?>
</ul>
