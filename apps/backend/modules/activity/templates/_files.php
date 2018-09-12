<ul class="sf_admin_actions">
<?php foreach($activity->getFiles() as $n => $file): ?>
  <li>
    <a target="_blank" href="<?php echo url_for('@agreement_model_download_file?file='.$file->getFile()); ?>"><?php echo $file->getName() ?></a>
    <ul>
      <li class="sf_admin_action_delete">
        <a href="<?php echo url_for('activity_file/delete/?id='.$file->getId()) ?>" onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
      </li>
    </ul>
  </li>
<?php endforeach; ?>
  <li class="sf_admin_action_new"><a href="<?php echo url_for('activity_file/new?activity_id='.$activity->getId()) ?>">Добавить</a></li>
</ul>
