<ul class="nav nav-list">
  <li class="nav-header"><?php echo !$section ? "Добавить раздел" : "Параметры"; ?></li>
</ul>

<form class="form-horizontal" id='frmSection'>
<div class="control-group">
  <label class="control-label" for="txtSectionName"></label>
  <div class="controls" style="margin-left: 0px;">
    <input type="text" id="txtSectionName" name="txtSectionName" placeholder="Название раздела" value='<?php echo $section ? $section->getHeader() : ''; ?>'>
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="sbSectionParent"></label>
  <div class="controls" style="margin-left: 0px;">
    <select id='sbSectionParent' name='sbSectionParent'>
      <option value='-1'>Основной раздел</option>
      <?php foreach($sections as $item): ?>
        <option value='<?php echo $item->getId(); ?>' <?php echo $section && $section->getParentId() == $item->getId() ? 'selected' : ''; ?>><?php echo $item->getHeader(); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
<div class="control-group">
  <div class="controls" style="margin-left: 0px;">
    <?php if(!$section): ?>
      <button id="btAddStatisticNewSection" type="submit" class="btn">Добавить</button>
    <?php else: ?>
      <input type='hidden' id='id' name='id' value='<?php echo $section->getId(); ?>' />
      <button id="btEditSection" type="submit" class="btn" data-id='<?php echo $section->getId(); ?>'>Изменить</button>
    <?php endif; ?>
  </div>
</div>
</form>