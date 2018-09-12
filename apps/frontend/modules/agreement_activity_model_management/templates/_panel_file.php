<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 22.09.2015
 * Time: 11:35
 */
?>
<div class="file">
    <div class="modal-file-wrapper input">
        <div class="control">
            <div class="caption">Перетащите сюда файлы или нажмите на кнопку для загрузки</div>
            <div class="green button modal-zoom-button modal-form-button model-main-file"></div>
            <input type="file" data-name="agreement_comments_file"
                   name="agreement_comments_file[]" multiple size="1"
                   data-container-cls="model-form-selected-files-to-upload">
        </div>
        <div class="modal-input-error-icon error-icon"></div>
        <div class="error message" style="display: none;"></div>
    </div>
    <div class="value file-name" style="margin: 5px 0 0;padding:0;border:0"></div>
    <div class="modal-form-uploaded-file"></div>

</div>

<div class="model-form-selected-files-to-upload"></div>