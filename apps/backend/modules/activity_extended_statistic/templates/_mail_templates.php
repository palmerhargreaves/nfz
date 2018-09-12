<ul class="nav nav-list" style="width: 90%;">
  <li class="nav-header">Шаблоны писем</li>

  <?php foreach($templates as $template): ?>
  <li>
    <a href="javascript:;" class='on-get-template' style='float:left; width: 90%;' 
    						data-id='<?php echo $template->getId(); ?>'
    						data-text='<?php echo $template->getMsg(); ?>'><?php echo Utils::trim_text($template->getMsg(), 150); ?></a>
  </li>
  <?php endforeach; ?>
</ul>