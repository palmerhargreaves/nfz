<ul class="sf_admin_actions">
<?php foreach($material->getWebPreviews() as $n => $preview): ?>
  <li>
    <a target="_blank" href="/uploads/<?php echo MaterialWebPreview::FILE_PATH, '/', $preview->getFile() ?>">
      <img src="/uploads/<?php echo MaterialWebPreview::FILE_PATH, '/', $preview->getFile() ?>" width="75" alt="" style="margin-left: 5px;" />
    </a>
    <ul>
      <li class="sf_admin_action_delete">
        <a href="<?php echo url_for('material_web_preview/delete/?id='.$preview->getId()) ?>" onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
      </li>
    </ul>
  </li>
<?php endforeach; ?>
  <li class="sf_admin_action_new"><a href="<?php echo url_for('material_web_preview/new?material_id='.$material->getId()) ?>">Добавить</a></li>
</ul>
