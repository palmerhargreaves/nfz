<div class="modal-header"><?php echo $data->getHeader();?></div>
<div class="modal-close modal-close-many"></div>
<div class="modal-text"><?php echo $data->getRawValue()->getDescription(); ?></div>    

<div class='model'>
    <div class="summer-action-form-eror-msg message-error" style="color: red; margin: auto; width: 95%; display: none;">
        <p></p>
    </div>

    <div style="display: block; width: 75%; margin: auto; margin-top: 40px;">

        <button class="button accept-button" style="width: 45%; float: left; clear: both;" ><?php echo 'Будем участвовать'; ?></button>

        <button class="button gray decline-button" style="width: 45%; float: right;"
                    data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'
                    data-act-type='decline'
                    data-dialog-id='<?php echo $data->getId(); ?>'><?php echo 'Не будем участвовать'; ?></button>
    </div>
</div>

<div id="containerAcceptServiceAction" style='margin-top: 115px; display: none;'>
    <div class="modal-text">
        <?php echo $data->getRawValue()->getConfirmMsg(); ?>
    </div>

    <div class='model'>
        <form id='frmDate' action='<?php echo url_for(); ?>'>
            <div style="display: block; width: 75%; margin: auto; margin-top: 10px;">
                <table style='width: 100%;'>
                    <?php if(!$data->getWithoutDates()): ?>
                    <tr class="model-mode-field">
                        <td class="field controls">
                            <div class="modal-input-wrapper input" style='width: 76%; margin-bottom: 7px;' >
                                <input name='startDate' type='text' class='with-date' placeholder='Дата начала акции'>
                                <div class="modal-input-error-icon error-icon"></div>
                                <div class="error message" style='display: none; z-index: 1;'></div>
                                
                            </div>
                        </td>
                        <td  class="field controls">
                            <div class="modal-input-wrapper input" style='width: 76%; margin-bottom: 7px; float: right;' >
                                <input name='endDate' type='text' class='with-date'  placeholder='Дата конца акции' >
                                <div class="modal-input-error-icon error-icon"></div>
                                <div class="error message" style='display: none; z-index: 1;'></div>
                                
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                
                    <tr>
                        <td><button class="button accept-button2" style="width: 85%; float: left; clear: both;" 
                                        data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'
                                        data-dialog-id='<?php echo $data->getId(); ?>'
                                        data-act-type='accept'><?php echo 'Подтвердить участие'; ?></button></td>

                        <td><button class="button gray decline-button2" style="width: 85%; float: right;"
                                        data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'
                                        data-dialog-id='<?php echo $data->getId(); ?>'><?php echo 'Отменить'; ?></button></td>
                    </tr>

                </table>
            </div>
        </form>
    </div>
</div>

<script>
    $(function() {
        $('#frmDate .with-date').datepicker();
    });
</script>