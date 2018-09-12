<ul class="sf_admin_actions">
<?php 
foreach($activity->getActivityStatisticPeriods() as $item): ?>
  <li>
    <a href="<?php echo url_for('activity_statistic_periods/edit/?id='.$item->getId()) ?>"><?php echo sprintf('%s (%s)', $item->getYear(), $item->getQuarters()) ?></a>
    <ul>
      <li class="sf_admin_action_delete">
        <a href="<?php echo url_for('activity_statistic_periods/delete/?id='.$item->getId()) ?>" onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
      </li>
    </ul>
  </li>
<?php endforeach; ?>
  <li class="sf_admin_action_new"><a href="<?php echo url_for('activity_statistic_periods/new?activity_id='.$activity->getId()) ?>">Добавить</a></li>
</ul>
