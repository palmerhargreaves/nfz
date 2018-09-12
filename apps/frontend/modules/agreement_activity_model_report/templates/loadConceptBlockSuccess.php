<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.05.2016
 * Time: 17:44
 */

$files = $model->getFinancialDocsFiles();
$report = $model->getReport();

foreach ($files as $file):
    ?>

    <div class="file">
        <div class="modal-file-wrapper input" style="display: inline-block;">
            <div class="control mod-popup-upload" style="width:auto;height:auto;">
                <span class="mod-popup-upload-caption">Перетащите сюда файл или  нажмите на кнопку для загрузки</span>
                <div class="modal-zoom-button modal-form-button button"
                     style="display:none"></div>
                <input type="file" name="<?php echo $file->getFieldName(); ?>" size="1"
                       data-file-index="<?php echo $file->getId(); ?>"
                       data-ext-model-file='1'>
            </div>
            <div class="modal-input-error-icon error-icon"></div>
            <div class="error message"></div>
            <!--<div class="modal-form-requirements">Допустимый формат: zip</div>-->

            <div class="value file-name">
                <a href="<?php echo url_for('@agreement_model_report_download_financial_file?file=' . $file->getFile()) ?>"
                   target="_blank"><?php echo $file->getFile(), ' (', $report->getFinancialDocsFileNameHelperByName($file->getFile())->getSmartSize() . ')' ?></a>
            </div>
            <?php if ($model->getStatus() == "accepted" && !in_array($report->getStatus(), array('accepted', 'wait'))): ?>
                <img class="remove-report-ext-file" src="/images/delete-icon.png" title="Удалить файл"
                     style="float: right; margin-top: 10px; margin-right: 5px; cursor: pointer;"
                     data-file-id="<?php echo $file->getId(); ?>"
                     data-field-name="financial_docs_file"
                     data-concept="1"/>
            <?php endif; ?>

            <div class="cl"></div>
            <div class="modal-form-uploaded-file"></div>
        </div>
    </div>

<?php endforeach; ?>