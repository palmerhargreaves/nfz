<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 22.06.2016
 * Time: 16:15
 */

$group_field_count = 0;
$field_ind = 0;

$n = 1;
if (count($fields) > 0 && !$owner && $include_group && $allow_to_edit): ?>
    <tr class="sorted-row model-row<?php if ($n++ % 2 == 0) echo ' even'; ?>">
        <td colspan="2" style="text-align: center;">
            <div id="bt_add_new_group_fields" data-group-id="<?php echo $group->getId(); ?>"
                 data-header-id="<?php echo $header->getId(); ?>" class="add small button"
                 style="float: right; margin-right: 10px;"><?php echo $group->getHeader(); ?></div>
        </td>
    </tr>
<?php endif; ?>

<?php

$hash_id = '';
foreach ($fields as $field):
    $first_field = false;

    $fieldValue = $field->getFieldValue($sf_user->getAuthUser()->getRawValue(), $current_q);
    if ($field->getGroupId() != 0) {
        $group_data = $field->getActivityVideoRecordsStatisticsHeadersGroups();
        $group_field_count = $group_data->getFieldsList($field->getParentHeaderId())->count();
    }

    if (empty($hash_id) || $hash_id != $field->getHashId()) {
        $hash_id = $field->getHashId();
        $first_field = true;
    }

    ?>

    <tr class="hash-<?php echo $field->getHashId(); ?> sorted-row model-row<?php if ($n++ % 2 == 0) echo ' even'; ?>">
        <?php $description = $field->getRawValue()->getDescription(); ?>
        <td style="width:605px; font-weight: bold; padding-left: 22px;  <?php echo !empty($description) ? 'border-bottom: 0px;' : ''; ?>">
            <?php echo $field->getName(); ?>
        </td>

        <td class="darker" style="<?php echo !empty($description) ? 'border-bottom: 0px;' : ''; ?>">
            <?php if ($field->getType() == ActivityVideoRecordsStatisticsHeadersFields::FIELD_TYPE_STRING): ?>
                <div class="modal-input-wrapper input"
                     style='margin: 7px; float: left;'>
                    <input type='text'
                           class='field-<?php echo $field->getId(); ?>'
                           placeholder='введите текст'
                           style='height: 24px; padding: 5px; width: 230px;'
                           data-regexp="/^[0-9a-zA-Zа-яА-Я]+$/"
                           data-field-id="<?php echo $field->getId(); ?>"
                           data-type="<?php echo $field->getType(); ?>"
                           value="<?php echo $fieldValue ? $fieldValue->getVal() : ''; ?>"
                        <?php echo !$allow_to_edit ? "disabled" : ""; ?>
                        <?php echo $field->getReq() ? "required" : ""; ?>>

                    <div
                            class="modal-input-error-icon error-icon"></div>
                    <div class="error message-modal"
                         style="display: none;"></div>
                </div>

                <?php if ($field->getOwner() != 0 && $first_field && $allow_to_edit): ?>
                    <div style="float: right; margin-top: 20px; margin-right: 3px;">
                        <img data-hash="<?php echo $field->getHashId(); ?>" class="on-delete-video-record-field"
                             data-field-id="<?php echo $field->getId(); ?>"
                             src="/images/delete-icon.png" title="Удалить поля">
                    </div>
                <?php endif; ?>
            <?php elseif ($field->getType() == ActivityVideoRecordsStatisticsHeadersFields::FIELD_TYPE_NUMBER): ?>
                <div class="modal-input-wrapper input"
                     style='margin: 7px; float: left;'>
                    <input type='text'
                           class='field-<?php echo $field->getId(); ?>'
                           placeholder='0'
                           style='height: 24px; padding: 5px; width: 230px;'
                           data-regexp="/^[0-9.]+$/"
                           data-field-id="<?php echo $field->getId(); ?>"
                           data-type="<?php echo $field->getType(); ?>"
                           value="<?php echo $fieldValue ? $fieldValue->getVal() : ''; ?>"
                        <?php echo !$allow_to_edit ? "disabled" : ""; ?>
                        <?php echo $field->getReq() ? "required" : ""; ?>>

                    <div
                            class="modal-input-error-icon error-icon"></div>
                    <div class="error message-modal"
                         style="display: none;"></div>
                </div>

                <?php if ($field->getOwner() != 0 && $first_field && $allow_to_edit): ?>
                    <div style="float: right; margin-top: 20px; margin-right: 3px;">
                        <img data-hash="<?php echo $field->getHashId(); ?>" class="on-delete-video-record-field"
                             data-field-id="<?php echo $field->getId(); ?>"
                             src="/images/delete-icon.png" title="Удалить поля">
                    </div>
                <?php endif; ?>
            <?php elseif ($field->getType() == ActivityVideoRecordsStatisticsHeadersFields::FIELD_TYPE_DATE):

                $period = explode("-", $fieldValue ? $fieldValue->getVal() : '');
                ?>
                <div class="modal-input-wrapper input"
                     style='width: 100px; margin: 7px; float: left;'>
                    <input type='text' name="periodStart"
                           class='with-date'
                           style='height: 24px; padding: 5px; width: 110px;'
                           placeholder='От'
                           value="<?php echo isset($period[0]) ? $period[0] : ''; ?>"
                           data-regexp="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$"
                           data-field-id="<?php echo $field->getId(); ?>"
                           data-type="<?php echo $field->getType(); ?>"
                        <?php echo !$allow_to_edit ? "disabled" : ""; ?>
                        <?php echo $field->getReq() ? "required" : ""; ?>>
                    <div
                            class="modal-input-error-icon error-icon"></div>
                    <div class="error message"
                         style='display: none; z-index: 1;'></div>
                </div>
                <div class="modal-input-wrapper input"
                     style='width: 124px; margin: 7px; float: right;'>
                    <input type='text' name="periodEnd"
                           class='with-date'
                           style='height: 24px; padding: 5px; width: 110px;'
                           placeholder='До'
                           value="<?php echo isset($period[1]) ? $period[1] : ''; ?>"
                           data-regexp="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$"
                           data-field-id="<?php echo $field->getId() ?>"
                           data-type="<?php echo $field->getType(); ?>"
                        <?php echo !$allow_to_edit ? "disabled" : ""; ?>
                        <?php echo $field->getReq() ? "required" : ""; ?>>
                    <div
                            class="modal-input-error-icon error-icon"></div>
                    <div class="error message"
                         style='display: none; z-index: 1;'></div>
                </div>
            <?php elseif ($field->getType() == ActivityVideoRecordsStatisticsHeadersFields::FIELD_TYPE_BOOL): ?>

            <?php elseif ($field->getType() == ActivityVideoRecordsStatisticsHeadersFields::FIELD_TYPE_FILE): ?>
                <div class="template-popup-form">
                    <div class="file">
                        <div class="modal-file-wrapper input">
                            <div class="control">
                                <div class="caption">Перетащите сюда файлы или нажмите на кнопку для загрузки
                                </div>
                                <div class="green button modal-zoom-button modal-form-button model-main-file"></div>
                                <input type="file" class="js-dealer-statistics-upload-file" name="field_file_<?php echo $field->getId(); ?>" style="height: 100px;"
                                    <?php echo !$allow_to_edit ? "disabled" : "" ; ?>
                                       class='field-<?php echo $field->getId(); ?>' size="1"
                                    <?php echo $field->getReq() && !$fieldValue ? "required" : ""; ?>
                                >
                            </div>
                            <div class="modal-input-error-icon error-icon"></div>
                            <!--<div class="modal-form-requirements">Допустимый формат: jpg</div>-->
                        </div>
                        <?php if ($sf_user->getAttribute('editor_link')): ?>
                            <div><a href='<?php echo $sf_user->getAttribute('editor_link'); ?>'
                                    target='_blank'><?php echo $sf_user->getAttribute('editor_link'); ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="modal-form-uploaded-file"></div>
                        <div class="model-form-selected-files-to-upload"></div>

                        <?php if ($sf_user->getAttribute('editor_link')): ?>
                            <div><a href='<?php echo $sf_user->getAttribute('editor_link'); ?>'
                                    target='_blank'><?php echo $sf_user->getAttribute('editor_link'); ?></a></div>
                        <?php endif; ?>
                        <div class="value file-name">
                            <?php if ($fieldValue): ?>
                                <a href="<?php echo url_for('@on_download_activity_field_file?id=' . $fieldValue->getId()); ?>">
                                    <?php echo $fieldValue->getVal(); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

            <?php endif; ?>
        </td>
    </tr>

    <?php
    if (!empty($description)): ?>
        <tr class="statistic-item" style="padding: 5px;">
            <td colspan="2"><span
                        style="font-size: 10px; font-weight: normal; float: left; margin: 3px; padding-left: 10px; padding-right: 10px; text-align: justify;"><i><?php echo $description; ?></i></span>
            </td>
        </tr>
    <?php endif; ?>

    <?php
    if ($field->getOwner() != 0) {
        $field_ind++;

        if ($field_ind == $group_field_count) {
            $field_ind = 0;

            ?>
            <tr style="">
                <td colspan="2" style="background-color: #e4e4e4; height: 10px; border-bottom: none;"></td>
            </tr>
            <?php
        }
    }
    ?>
<?php endforeach; ?>

<?php if (count($fields) > 0 && !$owner && $include_group): ?>
    <tr style="">
        <td colspan="2" style="background-color: #e4e4e4; height: 10px; border-bottom: none;"></td>
    </tr>
<?php endif; ?>
