
<table class="model-data" data-model-status="<?php echo $model->getStatus() ?>" data-css-status="<?php echo $model->getCssStatus() ?>" data-is-concept="<?php echo $model->isConcept() ? 'true' : 'false' ?>">
  <?php if(!$model->isConcept()): ?>
    <tr>
        <td class="label">
            Номер
        </td>
        <td class="value">
          <?php echo $model->getId() ?>
        </td>
    </tr>
  <?php endif; ?>
    <tr>
        <td class="label">
            Дилер
        </td>
        <td class="value">
          <?php echo $model->getDealer()->getName() ?>
        </td>
    </tr>
    <tr>
        <td class="label">
            Активность
        </td>
        <td class="value">
          <?php echo $model->getActivity()->getName() ?>
        </td>
    </tr>
  <?php if(!$model->isConcept()): ?>
    <tr>
        <td class="label">
            Название
        </td>
        <td class="value">
          <?php echo $model->getName() ?>
        </td>
    </tr>
    <tr>
        <td class="label">
            Тип размещения
        </td>
        <td class="value">
          <?php echo $model->getModelType()->getName() ?>
        </td>
    </tr>
    <tr>
        <td class="label">
            Цель
        </td>
        <td class="value">
          <?php echo $model->getTarget() ?>
        </td>
    </tr>
  <?php endif; ?>
    
  <?php foreach($model->getModelType()->getFields() as $field): 
    $val = $model->getValueByType($field->getIdentifier());
    if(!empty($val)):
  ?>
    <tr class="<?php echo $field->getHide() == 1 ? "ext-type-field" : ""; ?> type-fields-<?php echo $field->getModelTypeId(); ?>" 
                    data-field-type="<?php echo $field->getModelTypeId(); ?>" 
                    data-is-hide="<?php echo $field->getHide(); ?>">
        <td class="label">
            <?php echo $field->getName() ?><?php if($field->getUnits()): ?>, <?php echo $field->getUnits() ?><?php endif; ?>
        </td>
        <td class="value">
          <?php echo $model->getValueByType($field->getIdentifier()) ?>
        </td>
    </tr>
  <?php endif; ?>

  <?php endforeach; ?>
    
  <?php if(!$model->isConcept()): ?>
    <tr>
        <td class="label">
            Сумма
        </td>
        <td class="value">
          <?php echo $model->getCost() ?>
        </td>
    </tr>
  <?php endif; ?>

  <?php
    if($model->getAcceptInModel() != 0) {
  ?>
  <tr>
        <td class="label">
          Пролонгация заявки №
        </td>
        <td class="value">
          <?php echo $model->getAcceptInModel(); ?>
        </td>
  </tr>
  <?php } ?>

    <tr>
        <td class="label">
            <?php 
              if($model->getModelType()->getId() == 4)
                echo "Сценарий видеоролика";
              else if($model->getModelType() == 2)
                echo "Сценарий радиоролика";
              else
                echo $model->isConcept() ? 'Концепция' : 'Макет';
            ?>
        </td>
        <td class="value">
            <div class="modal-form-uploaded-file">
<?php if($model->getModelFile()): ?>
                <a href="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH.'/'.$model->getModelFile() ?>" target="_blank"><?php echo $model->getModelFile(), ' (', $model->getModelFileNameHelper()->getSmartSize().')' ?></a>
<?php endif; ?>
            </div>
        </td>
    </tr>

  <?php 
    if($model->getModelType()->getId() == 4) {
  ?>
    <tr>
        <td class="label">
           
        </td>
        <td class="value">
            <div class="modal-form-uploaded-file">
            <?php if($model->getModelRecordFile()): ?>
              <a href="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH.'/'.$model->getModelRecordFile() ?>" target="_blank"><?php echo $model->getModelRecordFile(), ' (', $model->getModelRecordFileNameHelper()->getSmartSize().')' ?></a>
            <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php } ?>

    <tr>
        <td class="label">
            В макет не вносились изменения
        </td>
        <td class="check" >
            <input type="checkbox" name="no_model_changes" <?php echo $model->getNoModelChanges() ? "checked" : ""; ?> data-required="false" style="width: 14px; float: left;">
        </td>
    </tr>

     <tr>
        <td class="label">
            Макет выполнен при помощи онлайн-редактора
        </td>
        <td class="check" >
            <input type="checkbox" name="model_accepted_in_online_redactor" <?php echo $model->getModelAcceptedInOnlineRedactor() ? "checked" : ""; ?> data-required="false" style="width: 14px; float: left;">
        </td>
    </tr>
    
</table>    
<div class="buttons">

<?php if(!$model->isOutOfDate()) { ?>
  <?php if($model->getStatus() != 'not_sent'): ?>
    <?php if($model->getStatus() != 'accepted'): ?>
              <div class="specialists button float-left modal-form-button"><a href="#" class="specialists">Отправить специалистам</a></div>
    <?php endif; ?>

    <?php if($model->getStatus() != 'accepted'): ?>
              <div class="accept green button float-left modal-form-button"><a href="#" class="accept">Согласовать</a></div>
    <?php endif; ?>
    <?php if($model->getStatus() != 'declined'): ?>
              <div class="decline gray button float-right modal-form-button"><a href="#" class="decline">Отклонить</a></div>
    <?php endif; ?>

    <div style="margin: auto; text-align: center; padding-top: 55px;">
      <a style="font-size: 11px; color: black;" href="<?php echo url_for('@discussion_switch_to_dealer?dealer='.$model->getDealerId().'&activityId='.$model->getActivityId().'&modelId='.$model->getId()); ?>" target='_blank'>
        Перейти в активность
      </a>
    </div>
    
              <div class="clear"></div>
  <?php endif; ?>
<?php } else { ?>
	<div class="dummy gray msg modal-form-button">Заявка заблокирована</div>

	<div class='out-of-date' data-out='true'></div>
	<div style="margin: auto; text-align: center; padding-top: 27px;">
      <a style="font-size: 11px; color: black;" href="<?php echo url_for('@discussion_switch_to_dealer?dealer='.$model->getDealerId().'&activityId='.$model->getActivityId().'&modelId='.$model->getId()); ?>" target='_blank'>
        Перейти в активность
      </a>
    </div>
<?php } ?>

	

</div>

<script>
  $(function() {
     
  });

</script>