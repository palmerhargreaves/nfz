<ul class="nav nav-list">
  <li class="nav-header"><?php echo !$field ? "Добавить поле" : "Параметры"; ?></li>
</ul>

<form class="form-horizontal" id="frmField">
  <div class="control-group">
    <label class="control-label" for="txtFieldName">Название поля</label>
    <div class="controls">
      <input type="text" id="txtFieldName" name="txtFieldName" placeholder="Название поля">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="txtFieldDescription">Краткое описание поля</label>
    <div class="controls">
      <input type="text" id="txtFieldDescription" name="txtFieldDescription" placeholder="Описание поля">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="sbFieldParent">Выберите раздел</label>
    <div class="controls">
      <select id='sbFieldParent' name='sbFieldParent'>
      <?php foreach($sections as $item): ?>
        <option value='<?php echo $item->getId(); ?>' <?php echo $field && $field->getParentId() == $item->getId() ? 'selected' : ''; ?>><?php echo $item->getHeader(); ?></option>
      <?php endforeach; ?>
    </select>
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="sbFieldType">Тип поля</label>
    <div class="controls">
      <select id='sbFieldType' name='sbFieldType'>
        <option value='date'>Дата</option>
        <option value='dig'>Значение</option>
        <option value='calc'>Вычисляемое поле</option>
        <option value='text'>Текст</option>
      </select>
    </div>
  </div>

  <div id="container-for-calculated-fields" style='display: none;'>
    <div class="control-group">
      <label class="control-label" for="sbFieldCalcFields">Вычисляемые поля</label>
      <div class="controls">
        <select id='sbFieldCalcFields' name='sbFieldCalcFields' size='10' style='width: 800px;'>
        <?php 
          $sections = ActivityExtendedStatisticSectionsTable::getInstance()->createQuery()->orderBy('id ASC')->execute();

          foreach($sections as $section):
            $fields = $section->getFields();
          ?>
            <optgroup label="<?php echo $section->getHeader(); ?>">
          <?php
            foreach($fields as $fItem):
              if($fItem->getValueType() == 'date') continue;
           ?>
            <option value='<?php echo $fItem->getId(); ?>'><?php echo $fItem->getHeader(); ?></option>
          <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
        </select>

      </div>
    </div>

    <div class="control-group">
      <div class="controls">
        <button id='btFieldAddCalcField' class='btn btn-success btn-mini' style=''>Добавить</button>
      </div>

      <div class="controls container-calculated-fields-to-add" style='margin-top: 10px;'></div>
    </div>

     <div class="control-group">
      <label class="control-label" for="sbCalcFieldsAction">Действие</label>
      <div class="controls">
        <select id='sbCalcFieldsAction' name='sbCalcFieldsAction'>
          <option value='<?php echo ActivityExtendedStatisticFields::FIELD_CALC_SYMBOL_PLUS; ?>'>+</option>
          <option value='<?php echo ActivityExtendedStatisticFields::FIELD_CALC_SYMBOL_MINUS; ?>'>-</option>
          <option value='<?php echo ActivityExtendedStatisticFields::FIELD_CALC_SYMBOL_DIVIDE; ?>'>/</option>
          <option value='<?php echo ActivityExtendedStatisticFields::FIELD_CALC_SYMBOL_PERCENT; ?>'>%</option>
        </select>
      </div>
    </div>

  </div>

  <div class="control-group">
    <div class="controls" style="margin-left: 3px;">
      <?php if(!$field): ?>
        <button id="btAddStatisticNewField" type="submit" class="btn">Добавить</button>
      <?php else: ?>
        <input type='hidden' id='id' name='id' value='<?php echo $field->getId(); ?>' />
        <button id="btEditField" type="submit" class="btn" data-id='<?php echo $field->getId(); ?>'>Изменить</button>
      <?php endif; ?>
    </div>
  </div>
</form>
