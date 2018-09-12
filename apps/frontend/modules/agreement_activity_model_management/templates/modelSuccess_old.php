<?php 
  $totalFiles = 10;
?>
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
            Название материала
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
          <?php //echo Utils::trim_text($model->getValueByType($field->getIdentifier()), 40); ?>
          <?php echo $model->getValueByType($field->getIdentifier()); ?>
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
              else if($model->getModelType()->getId() == 2)
                echo "Сценарий радиоролика";
              else
                echo $model->isConcept() ? 'Концепция' : 'Макет';
            ?>

            <?php
              /*if(($model->getModelRecordFile() && $model->getModelRecordFile() != '-')
                  && ($model->getModelType()->getId() == 2 || $model->getModelType()->getId() == 4) 
                  && ($model->getStep1() == 'accepted')
                  && ($model->getStep2() == 'wait' || $model->getStep2() == 'accepted' || $model->getStep2() == 'none'))*/
              if($model->getStep1() == "accepted" && ($model->getModelType()->getId() == 2 || $model->getModelType()->getId() == 4) )
                echo "<div style='float: right; margin-right: 10px;'><img src='/images/ok-icon-active.png' title='Запись радиоролика загружена' /></div>";
            ?>
        </td>
        <td class="value">
            <div class="modal-form-uploaded-file">
<?php if($model->getModelFile()): ?>
      <?php if($model->getEditorLink()): ?>
            <a href="<?php echo $model->getModelFile() ?>" target="_blank"><?php echo $model->getModelFile(), ' (', Utils::getRemoteFileSize($model->getModelFile()).')' ?></a>
      <?php else: ?>
            <a href="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH.'/'.$model->getModelFile() ?>" target="_blank"><?php echo $model->getModelFile(), ' (', $model->getModelFileNameHelper()->getSmartSize().')' ?></a>
      <?php endif; ?>
<?php endif; ?>
            </div>
        </td>
  </tr>

  <?php
    
    for($i = 1; $i <= $totalFiles; $i++):
      $func = "getModelFile".$i;
      $file = $model->$func();

      $label = $model->isConcept() ? "Концепция" : "Макет";
      if($file):

        if($model->getModelType()->getId() == 4)
          $label = "Сценарий видеоролика";
        else if($model->getModelType()->getId() == 2)
          $label = "Сценарий радиоролика";
  ?>
  <tr>
        <td class="label">
            <?php echo sprintf($label." №%d", $i); ?>
        </td>
        <td class="value">
            <div class="modal-form-uploaded-file">
                <a href="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH.'/'.$file ?>" target="_blank"><?php echo $file, ' (', $model->getModelFileNameHelperByFileName($file)->getSmartSize().')' ?></a>
            </div>
        </td>
  </tr>  
  <?php 
      endif;
    endfor; 
  ?>

  <?php
    //if($model->getStep1() == 'accepted') {
    if($model->getModelRecordFile() && $model->getModelRecordFile() != 'Array' && $model->getModelRecordFile() != '-' ) {
  ?>
    <tr>
        <td class="label">
            <?php
              if($model->getModelType()->getId() == 4)
                echo "Запись видеоролика";
              else if($model->getModelType()->getId() == 2)
                echo "Запись радиоролика";
            ?>
        </td>
        <td class="value">
            <div class="modal-form-uploaded-file">
<?php if($model->getModelRecordFile() && $model->getModelRecordFile() != '-') { ?>
                <a href="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH.'/'.$model->getModelRecordFile() ?>" target="_blank"><?php echo $model->getModelRecordFile(), ' (', $model->getModelRecordFileNameHelper()->getSmartSize().')' ?></a>
<?php } else  { ?>
        -
<?php } ?>
            </div>
        </td>
    </tr>

    <?php
      for($i = 1; $i <= $totalFiles; $i++):
        $func = "getModelRecordFile".$i;
        $file = $model->$func();

        $label = "Макет";
        if($file):
          if($model->getModelType()->getId() == 4)
            $label = "Запись видеоролика";
          else if($model->getModelType()->getId() == 2)
            $label = "Запись радиоролика";
    ?>
    <tr>
          <td class="label">
              <?php echo sprintf($label." №%d", $i); ?>
          </td>
          <td class="value">
              <div class="modal-form-uploaded-file">
                  <a href="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH.'/'.$file ?>" target="_blank"><?php echo $file, ' (', $model->getModelFileNameHelperByFileName($file)->getSmartSize().')' ?></a>
              </div>
          </td>
    </tr>  
    <?php 
        endif;
      endfor; 
    ?>

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

  <?php if($model->getEditorLink()): ?>
    <tr>
        <td class="label">
            Ссылка на редактор
        </td>
        <td class="check" >
            <a href='<?php echo $model->getEditorLink(); ?>' target='_blank'>Перейти</a>
        </td>
    </tr>
  <?php endif; ?>
    
</table>    
<div class="buttons">

<?php if(!$model->getIsBlocked() || $model->getAllowUseBlocked()): ?>
    <?php if($model->getStatus() != 'accepted'): ?>
        <div class="specialists button float-left modal-form-button" style="margin-bottom: 5px;"><a href="#" class="specialists">Отправить специалистам</a></div>
    <?php endif; ?>
        
    <?php if($model->getStatus() != 'accepted'): ?>
            <?php if($model->getModelTypeId() != 2 && $model->getModelTypeId() != 4): ?>
                <div class="accept green button float-left modal-form-button" style="margin-bottom: 5px;"><a href="#" class="accept">Согласовать</a></div>
            <?php else:
            
              if($model->getStatus() == "wait" && ($model->getStep1() == "wait" || $model->getStep1() == "none")): ?>
                  <div class="accept green button float-left modal-form-button" style="margin-bottom: 5px;"><a href="#" class="accept">Согласовать сценарий</a></div>
              <?php endif;            
            
              if($model->getStatus() == "wait" && $model->getStep1() == "accepted" && ($model->getStep2() == "wait" || $model->getStep2() == "none")): ?>
                  <div class="accept green button float-right modal-form-button" style="margin-bottom: 5px;"><a href="#" class="accept">Согласовать запись</a></div>
              <?php endif; ?>

          <?php endif; ?>
    <?php endif; ?>
                
    <?php if($model->getStatus() != 'declined'): ?>
            <?php if($model->getModelTypeId() != 2 && $model->getModelTypeId() != 4): ?>
              <div class="decline gray button float-right modal-form-button"><a href="#" class="decline">Отклонить</a></div>
            <?php else: ?>
              
              <?php if($model->getStatus() == "accepted" && $model->getStep1() == "accepted" && ($model->getStep2() == "accepted" || $model->getStep2() == "none")): ?>
                <div class="decline gray button float-right modal-form-button"><a href="#" class="decline">Отклонить</a></div>
              <?php endif; ?>

              <?php if($model->getStatus() != "accepted" && ($model->getStep1() == "accepted" || $model->getStep1() == "wait" || $model->getStep1() == "none")): ?>
                  <div style='float: left;' class="decline gray button float-right modal-form-button" data-step="first"><a href="#" class="decline">Отклонить сценарий</a></div>
              <?php endif;
              
              if($model->getStatus() == "wait" && $model->getStep1() == "accepted"  && ($model->getStep2() == "wait" || $model->getStep2() == "none")): ?>
                  <div class="decline gray button float-right modal-form-button" data-step="second"><a href="#" class="decline">Отклонить запись</a></div>
              <?php endif; ?>
            <?php endif; ?>
    <?php endif; ?>

    <div style="margin: auto; text-align: center; padding-top: 20px; display: block; width: 100%; float:left;">
      <a style="font-size: 11px; color: black;" href="<?php echo url_for('@discussion_switch_to_dealer?dealer='.$model->getDealerId().'&activityId='.$model->getActivityId().'&modelId='.$model->getId()); ?>" target='_blank'>
        Перейти в активность
      </a>
    </div>
   
   <div class="clear"></div>
<?php else: ?>
	<div class="dummy gray msg modal-form-button">Заявка заблокирована</div>

	<div class='out-of-date' data-out='true'></div>
	<div style="margin: auto; text-align: center; padding-top: 27px;">
      <a style="font-size: 11px; color: black;" href="<?php echo url_for('@discussion_switch_to_dealer?dealer='.$model->getDealerId().'&activityId='.$model->getActivityId().'&modelId='.$model->getId()); ?>" target='_blank'>
        Перейти в активность
      </a>
    </div>
<?php endif; ?>
</div>