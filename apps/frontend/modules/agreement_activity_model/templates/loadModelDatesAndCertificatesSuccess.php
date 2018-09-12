<?php
    $ind = 0;
    foreach($dates as $date): 
        list($start, $end) = explode("/", $date->getDateOf());
?>
<tr class="model-dates-field">
    <td class="label" style="width: 136px;">
        Даты проведения мероприятия <?php echo $ind != 0 ? '№'.$ind : ''; ?>
    </td>
    <td class="field controls">
    <?php if($model->getStatus() == "accepted" || $model->getStatus() == "wait" || $model->getStatus() == "wait_specialist") : ?>
        <div class="value"><?php echo sprintf('%s&nbsp;-&nbsp;%s', $start, $end); ?></div>
    <?php else: ?>
        <div class="modal-input-group-wrapper period-concept-group">
            <div class="modal-input-wrapper modal-short-input-wrapper">
                <input type="text" name="dates_of_service_action_start[]" class="dates-field" placeholder="от" data-format-expression="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$" data-required="1" data-right-format="21.01.2013" value="<?php echo str_replace('-', '.', date('d-m-Y', strtotime($start))); ?>" />
                <div class="modal-input-error-icon error-icon"></div>
              </div>
              <div class="modal-input-wrapper modal-short-input-wrapper">
                <input type="text" name="dates_of_service_action_end[]" class="dates-field" placeholder="до" data-format-expression="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$" data-required="1" data-right-format="21.01.2013" value="<?php echo str_replace('-', '.', date('d-m-Y', strtotime($end))); ?>" />
                <div class="modal-input-error-icon error-icon"></div>
                <div class="error message dates-error-message"></div>
            </div>
        </div>

        <?php if($ind == 0): ?>
            <div class='dates-add-field' style="font-size: 11px; text-align: center; cursor: pointer; text-decoration: underline; float: right; margin: 3px;">Добавить дату</div>
        <?php else: ?>
            <img class="remove-date-field" src="/images/delete-icon.png" title="Удалить дату" data-id='<?php echo $date->getId(); ?>' style="float: right; margin-top: 10px; margin-right: 5px; cursor: pointer; display: block;">
        <?php endif; ?>
    <?php endif; ?>
    </td>
</tr>
<?php $ind++; endforeach; ?>

<?php if($certificateDate): ?>
<tr class="model-certificate-field">
    <td class="label" style="width: 136px;">
        Срок окончания действия сертификата
    </td>
    <td class="field controls">
    <?php if($model->getStatus() == "accepted" || $model->getStatus() == "wait" || $model->getStatus() == "wait_specialist") : ?>
        <div class="value"><?php echo $certificateDate; ?></div>
    <?php else: ?>
        <div class="modal-input-wrapper">
          <input type="text" name="date_of_certificate_end" id="" class="dates-field" placeholder="" data-format-expression="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$" data-required="1" data-right-format="21.01.2013" value='<?php echo str_replace('-', '.', date('d-m-Y', strtotime($certificateDate))); ?>'/>
          <div class="modal-input-error-icon error-icon"></div>
          <div class="error message"></div>
        </div>
    <?php endif; ?>
    </td>
</tr>
<?php endif; ?>