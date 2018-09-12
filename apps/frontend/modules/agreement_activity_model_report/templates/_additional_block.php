<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.05.2016
 * Time: 14:00
 */

$files = $model->getAdditionalFiles();
foreach ($files as $file):

    ?>
    <tr class="additional-file model-form report-additional-docs file-item-<?php echo $file->getId(); ?>">
        <td style="padding: 0 0 10px;border:0;">
            <div class="field controls">
                <div class="file">
                    <div class="modal-file-wrapper input">
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
                    </div>

                    <div class="value file-name">
                        <a href="<?php echo url_for('@agreement_model_report_download_additional_file?file=' . $file->getFile()) ?>"
                           target="_blank"><?php echo $file->getFile(), ' (', $report->getAdditionalFileNameHelperByName($file->getFile())->getSmartSize() . ')' ?></a>
                    </div>
                    <?php if ($model->getStatus() == "accepted" && $report->getstatus() != "wait"): ?>
                        <img class="remove-report-ext-file" src="/images/delete-icon.png" title="Удалить файл"
                             style="float: right; margin-top: 10px; margin-right: 5px; cursor: pointer;"
                             data-file-id="<?php echo $file->getId(); ?>"
                             data-field-name="additional_file" />
                    <?php endif; ?>

                    <div class="cl"></div>
                    <div class="modal-form-uploaded-file"></div>
                </div>
            </div>
        </td>
    </tr>
    <?php
endforeach;
