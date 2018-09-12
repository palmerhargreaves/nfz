<?php if($sections && count($sections) > 0): ?>
<ul class="nav nav-list" style="width: 90%;">
  <li class="nav-header">Разделы</li>

  <?php foreach($sections as $section): ?>
    <li>
    	<a href="javascript:;" class='section-edit' style='float:left; width: 90%;' data-id='<?php echo $section->getId(); ?>'><?php echo $section->getHeader(); ?></a>
    	<img src='/images/delete-icon.png' class='delete-section' data-id='<?php echo $section->getId(); ?>' title='Удалить раздел' style='float: right; margin-top: 8px; cursor: pointer;' />
    </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>