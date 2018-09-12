<form action="<?php echo url_for("@agreement_module_models_add?activity={$activity->getId()}") ?>" class="form-horizontal" method="post" enctype="multipart/form-data" id="model-form" target="model-target">
  <input type="hidden" name="id"/>
  <input type="hidden" name="blank_id"/>
  <input type="hidden" name="draft" value="false"/>
  <div class="concept-form">
      <p class="description">Загрузите сюда концепцию проведения данной акции в вашем дилерском центре.<br />
      Файл должен содержать полную информацию по организации и проведению акции.</p>
      <div class="requirements">
          <ul>
            <li>Схема проведения акции</li>
            <li>Период действия акции</li>
            <li>Рекламные материалы для анонса</li>
            <li>Оформление дилерского центра</li>
            <li>Мотивация сотрудников предприятия в период проведения акции (необязательный пункт)</li>
            <li>Подарки клиентам</li>
            <li>Ожидаемый эффект</li>
          </ul>
      </div>
      <div class="controls field">
          <div class="file">
              <div class="modal-file-wrapper input">
                  <div class="control">
                      <div class="button modal-zoom-button modal-form-button"></div>
                      <input type="file" name="model_file" size="1">
                  </div>
                  <div class="modal-input-error-icon error-icon"></div>
                  <div class="error message"></div>
                  <!--<div class="modal-form-requirements">Допустимый формат: jpg</div>-->
              </div>
              <div class="value file-name"></div>
              <div class="clear"></div>
              <div class="modal-form-uploaded-file"></div>
          </div>
      </div>
  </div>
  
<table class="model-form">
    <tr class="model-mode-field number-field">
        <td class="label">
            Номер
        </td>
        <td class="field controls">
            <div class="value"></div>
        </td>
    </tr>
    <tr class="model-mode-field">
        <td class="label">
            Название
        </td>
        <td class="field controls">
            <div class="modal-input-wrapper input">
                <input type="text" value="" name="name" placeholder="" data-required="true">
                <div class="modal-input-error-icon error-icon"></div>
                <div class="error message"></div>
            </div>
            <div class="value"></div>
        </td>
    </tr>
    <tr class="model-mode-field">
        <td class="label">
            Тип
        </td>
        <td class="field controls model-type">
            <div class="modal-select-wrapper select input krik-select">
                <span class="select-value"><?php echo $model_types->getFirst()->getName() ?></span>
                <div class="ico"></div>
                <input type="hidden" name="model_type_id" value="<?php echo $model_types->getFirst()->getId() ?>">
                <div class="modal-input-error-icon error-icon"></div>
                <div class="error message"></div>
                <div class="modal-select-dropdown">
<?php foreach($model_types as $type): ?>
                    <div class="modal-select-dropdown-item select-item" data-value="<?php echo $type->getId() ?>"><?php echo $type->getName() ?></div>
<?php endforeach; ?>
                </div>
            </div>
            <div class="value"></div>
        </td>
    </tr>
    <tr class="model-mode-field">
        <td class="label">
            Цель
        </td>
        <td class="field controls">
            <div class="modal-input-wrapper input">
                <input type="text" value="" name="target" placeholder="" data-required="true">
                <div class="modal-input-error-icon error-icon"></div>
                <div class="error message"></div>
            </div>
            <div class="value"></div>
        </td>
    </tr>
<?php foreach($model_types_fields as $id => $fields): ?>
  <?php foreach($fields as $renderer): ?>
    <tr class="type-fields type-fields-<?php echo $id ?>">
        <td class="label">
            <?php echo $renderer->getField()->getName() ?><?php if($renderer->getField()->getUnits()): ?>, <?php echo $renderer->getField()->getUnits() ?><?php endif; ?>
        </td>
        <td class="field controls">
            <div class="input">
            <?php echo $renderer->getRawValue()->render() ?>
            </div>
            <div class="value"></div>
        </td>
    </tr>
  <?php endforeach; ?>
<?php endforeach; ?>
    <tr class="model-mode-field">
        <td class="label">
            Сумма без НДС, руб.
        </td>
        <td class="field controls">
            <div class="modal-input-wrapper input">
                <input type="text" value="" name="cost" placeholder="" data-format-expression="^[0-9]+(\.[0-9]+)?$" data-required="true" data-right-format="100.00">
                <div class="modal-input-error-icon error-icon"></div>
                <div class="error message"></div>
            </div>
            <div class="value"></div>
        </td>
    </tr>
    <tr>
        <td class="label file-label">
            Макет
        </td>
        <td class="field controls">
            <div class="file">
                <div class="modal-file-wrapper input">
                    <div class="control">
                        <div class="green button modal-zoom-button modal-form-button"></div>
                        <input type="file" name="model_file" size="1">
                    </div>
                    <div class="modal-input-error-icon error-icon"></div>
                    <div class="error message"></div>
                    <!--<div class="modal-form-requirements">Допустимый формат: jpg</div>-->
                </div>
                <div class="value file-name"></div>
                <div class="clear"></div>

                <div class="modal-form-uploaded-file"></div>
            </div>
        </td>
    </tr>
</table>
  <div class="buttons">
    <button class="float-left button modal-zoom-button modal-form-button modal-form-submit-button submit-btn" type="submit"><span>Отправить на согласование</span></button>
    <div class="float-right gray draft button modal-form-button draft-btn">Сохранить как черновик</div>
    <div class="clear"></div>
    <div style="margin: auto; margin-top: 15px;" class="delete gray button delete-btn">Удалить</div>
    <div style="margin: auto;" class="cancel gray button cancel-btn">Отменить отправку</div>
  </div>
</form>

<iframe src="/blank.html" width="1" height="1" frameborder="0" hspace="0" marginheight="0" marginwidth="0"  name="model-target" scrolling="no"></iframe>
