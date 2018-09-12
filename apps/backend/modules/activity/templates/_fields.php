<ul class="sf_admin_actions">
<?php 
foreach($activity->getActivityField() as $item): ?>
  <li>
    <a href="<?php echo url_for('activity_fields/edit/?id='.$item->getId()) ?>"><?php echo $item->getName() ?></a>
    <ul>
      <li class="sf_admin_action_delete">
        <a href="<?php echo url_for('activity_fields/delete/?id='.$item->getId()) ?>" onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
      </li>
    </ul>
  </li>
<?php endforeach; ?>
  <li class="sf_admin_action_new"><a href="<?php echo url_for('activity_fields/new?activity_id='.$activity->getId()) ?>">Добавить</a></li>
</ul>
