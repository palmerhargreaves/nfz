<?php if ( Utils::allowedIps() ): ?>
<form method="post" action="/" enctype="multipart/form-data" id="agreement-model-form" target="accept-decline-model-frame">
    <?php else: ?>
    <form method="post" action="/" enctype="multipart/form-data" target="accept-decline-model-frame" id="agreement-model-form">
        <?php endif; ?>

        <input type="hidden" name="id" value="<?php echo $model->getId(); ?>"/>
        <input type="hidden" name="action_type" value=""/>
        <input type="hidden" name="step" value=""/>

        <input type="hidden" name="<?php echo session_name(); ?>" value="<?php echo session_id(); ?>">
        <input type="hidden" name="upload_file_object_type" value=""/>
        <input type="hidden" name="upload_file_type" value=""/>
        <input type="hidden" name="upload_field" value=""/>
        <input type="hidden" name="upload_files_ids" value=""/>

        <div class="template-popup-form">
            <div class="template-popup-form-l">
                <table class="model-data" data-model-status="<?php echo $model->getStatus() ?>"
                       data-css-status="<?php echo $model->getCssStatus() ?>"
                       data-is-concept="<?php echo $model->isConcept() ? 'true' : 'false' ?>">
                    <?php if (!$model->isConcept()): ?>
                        <tr>
                            <td class="label">
                                Номер
                            </td>
                            <td class="field controls">
                                <div class="value"><?php echo $model->getId() ?></div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label">
                            Дилер
                        </td>
                        <td class="field controls">
                            <div class="value"><?php echo $model->getDealer()->getName() ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">
                            Активность
                        </td>
                        <td class="field controls">
                            <div class="value"><?php echo $model->getActivity()->getName() ?></div>
                        </td>
                    </tr>
                    <?php if (!$model->isConcept()): ?>
                        <tr>
                            <td class="label">
                                Название материала
                            </td>
                            <td class="field controls">
                                <div class="value"><?php echo $model->getName() ?></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">
                                Тип размещения
                            </td>
                            <td class="field controls">
                                <div class="value"><?php echo $model->getModelType()->getName() ?></div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($model->getModelType()->getFields() as $field):
                        $val = $model->getValueByType($field->getIdentifier());
                        if (!empty($val)):
                            ?>
                            <tr class="<?php echo $field->getHide() == 1 ? "ext-type-field" : ""; ?> type-fields-<?php echo $field->getModelTypeId(); ?>"
                                data-field-type="<?php echo $field->getModelTypeId(); ?>"
                                data-is-hide="<?php echo $field->getHide(); ?>">
                                <td class="label">
                                    <?php echo $field->getName() ?><?php if ($field->getUnits()): ?>, <?php echo $field->getUnits() ?><?php endif; ?>
                                </td>
                                <td class="field controls">
                                    <?php //echo Utils::trim_text($model->getValueByType($field->getIdentifier()), 40);
                                    ?>
                                    <div class="value"><?php echo $model->getValueByType($field->getIdentifier()); ?></div>
                                </td>
                            </tr>
                        <?php endif; ?>

                    <?php endforeach; ?>

                    <?php if (!$model->isConcept()): ?>
                        <tr>
                            <td class="label">
                                Сумма
                            </td>
                            <td class="field controls">
                                <div class="value"><?php echo $model->getCost() ?></div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php
                    if ($model->getAcceptInModel() != 0) {
                        ?>
                        <tr>
                            <td class="label">
                                Пролонгация заявки №
                            </td>
                            <td class="field controls">
                                <div class="value"><?php echo $model->getAcceptInModel(); ?></div>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php
                    if ($model->getActivity()->getAllowCertificate() && $model->getConceptId() != 0) {
                        $concept = $model->getConcept() ? $model->getConcept() : $model;
                        if ($concept) {
                            include_partial('model_dates', array( 'concept' => $concept ));
                        }
                    } else if ($model->getAgreementModelDates()->count() > 0) {
                        include_partial('model_dates', array( 'concept' => $model ));
                    }
                    ?>

                </table>
            </div>
            <div class="template-popup-form-r">

                <table>
                    <?php if ($model->getModelFile()): ?>
                        <tr>
                            <td colspan="2" class="field controls">
                                <div style="margin: 0 0 .5em;font-size:14px;color:#000;">
                                    <?php
                                    if ($model->getStep1() == "accepted" && ( $model->getModelType()->getId() == 2 || $model->getModelType()->getId() == 4 ))
                                        echo "<div style='float: right; margin-right: 10px;'><img src='/images/ok-icon-active.png' title='Запись радиоролика загружена' /></div>";
                                    ?>
                                </div>
                                <div class="">
                                    <div class="modal-form-uploaded-file" style="width:auto;max-width:300px;">
                                        <?php if ($model->getEditorLink()):
                                            $external = true;

                                            if (strrpos($model->getModelFile(), 'http') !== false) {
                                                $fileSize = Utils::getRemoteFileSize($model->getModelFile());
                                                if ($fileSize == 0)
                                                    $fileSize = $model->getModelFileNameHelper()->getSmartSize();
                                            } else {
                                                $external = false;
                                                $fileSize = Utils::getRemoteFileSize(AgreementModel::MODEL_FILE_PATH . '/' . $model->getModelFile());
                                                if ($fileSize == 0)
                                                    $fileSize = $model->getModelFileNameHelper()->getSmartSize();
                                            }

                                            if ($external):
                                                ?>
                                                <a href="<?php echo $model->getModelFile(); ?>"
                                                   target="_blank"><?php echo $model->getModelFile(), ' (' . $fileSize . ')' ?></a>
                                            <?php else: ?>
                                                <!--<a href="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH . '/' . $model->getModelFile() ?>" target="_blank"><?php echo $model->getModelFile(), ' (' . $fileSize . ')' ?></a>-->
                                                <a href="<?php echo url_for('@agreement_model_download_file?file=' . $model->getModelFile()) ?>"
                                                   target="_blank"><?php echo $model->getModelFile(), ' (' . $fileSize . ')' ?></a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <!--<a href="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH . '/' . $model->getModelFile() ?>" target="_blank"><?php echo $model->getModelFile(), ' (', $model->getModelFileNameHelper()->getSmartSize() . ')' ?></a>-->
                                            <a href="<?php echo url_for('@agreement_model_download_file?file=' . $model->getModelFile()); ?>"
                                               target="_blank"><?php echo $model->getModelFile(), ' (', $model->getModelFileNameHelper()->getSmartSize() . ')' ?></a>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php

                    $fileIndex = 0;
                    $files = $model->getModelUploadedFiles(AgreementModel::BY_SCENARIO);
                    foreach ($files as $file):
                        $label = $model->getLabel("Сценарий");
                        ?>
                        <tr>
                            <td colspan="2" class="field controls">
                                <div style="margin: 0 0 .5em;font-size:14px;color:#000;">
                                    <?php echo sprintf($label . " №%d", ++$fileIndex); ?>
                                    <?php
                                    if ($model->getStep1() == "accepted" && ( $model->getModelType()->getId() == 2 || $model->getModelType()->getId() == 4 ) && $fileIndex == 1)
                                        echo "<div style='float: right; margin-right: 10px;'><img src='/images/ok-icon-active.png' title='Сценарий радиоролика загружена' /></div>";
                                    ?>
                                </div>
                                <div class="">
                                    <div class="modal-form-uploaded-file" style="width:auto;max-width:300px;">
                                        <a href="<?php echo url_for('@agreement_model_download_file?file=' . $file->getId()) ?>"
                                           target="_blank"><?php echo $file->getFile(), ' (', $model->getModelFileNameHelperByFileName($file->getFileName())->getSmartSize() . ')' ?></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                    ?>
                </table>

                <table>
                    <?php
                    if (( $model->getStatus() != "not_sent" && $model->getStatus() != "wait_specialist" ) && $model->getStep1() == "accepted" && $model->getStep2() != "none") {
                        $fileIndex = 0;
                        $files = $model->getModelUploadedFiles(AgreementModel::BY_RECORD);
                        foreach ($files as $file):
                            $label = $model->getLabel("Запись");
                            ?>
                            <tr>
                                <td colspan="2" class="field controls">
                                    <div style="margin: 0 0 .5em;font-size:14px;color:#000;">
                                        <?php echo sprintf($label . " №%d", ++$fileIndex); ?>

                                        <?php
                                        if (( $model->getStep1() == "accepted" && $model->getStep2() == "accepted" ) && ( $model->getModelType()->getId() == 2 || $model->getModelType()->getId() == 4 ) && $fileIndex == 1)
                                            echo "<div style='float: right; margin-right: 10px;'><img src='/images/ok-icon-active.png' title='Запись радиоролика загружена' /></div>";
                                        ?>
                                    </div>
                                    <div class="">
                                        <div class="modal-form-uploaded-file" style="width:auto;max-width:300px;">
                                            <a href="<?php echo url_for('@agreement_model_download_file?file=' . $file->getId()) ?>"
                                               target="_blank"><?php echo $file->getFile(), ' (', $model->getModelFileNameHelperByFileName($file->getFileName())->getSmartSize() . ')' ?></a>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                        ?>
                    <?php } ?>

                    <?php
                    if (!$model->isConcept() && $model->getNoModelChanges()) {
                        ?>
                        <tr>
                            <td colspan="2" class="check">
                                <label for="no_model_changes">В макет не вносились изменения</label>
                                <!--<input type="checkbox" id="no_model_changes"
                               name="no_model_changes" <?php echo $model->getNoModelChanges() ? "checked" : ""; ?>
                               data-required="false"/>-->
                            </td>
                        </tr>


                    <?php } ?>

                    <?php if ($model->getEditorLink()): ?>
                        <tr>
                            <td colspan="2" class="check">
                                Ссылка на редактор <a href='<?php echo $model->getEditorLink(); ?>'
                                                      target='_blank'>Перейти</a>
                            </td>
                        </tr>
                    <?php endif; ?>

                </table>

                <table>
                    <tr>
                        <td>
                            <div class="d-popup-req-title"><strong>Файл</strong></div>

                            <div class="file">
                                <div class="modal-file-wrapper input">
                                    <div id="model-files-progress-bar"
                                         class="progress-bar-content progress-bar-full-width"></div>
                                </div>
                            </div>

                            <div class="scroller scroller-model" style="margin-bottom: 10px; height: 190px;">
                                <div class="scrollbar">
                                    <div class="track">
                                        <div class="thumb">
                                            <div class="end"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="viewport scroller-wrapper" style="height: 220px;">
                                    <div class="overview scroller-inner">

                                        <div class="file">
                                            <div class="modal-file-wrapper input">
                                                <div id="container_model_files" class="control dropzone"
                                                     style="min-height: 150px; width: auto !important;">
                                                    <div class="d-popup-files-wrap scrollbar-inner">
                                                        <div class="d-popup-files-row">
                                                            <div id="model_files"
                                                                 class="d-popup-uploaded-files d-cb"></div>
                                                            <input type="file" id="agreement_comments_file"
                                                                   name="agreement_comments_file" multiple>
                                                        </div>
                                                    </div>

                                                    <div class="d-popup-files-footer d-cb">
                                                        <a href="javascript:" id="js-file-trigger-model"
                                                           class="button js-d-popup-file-trigger"
                                                           data-target="#agreement_comments_file">Прикрепить
                                                            файл</a>
                                                        <div id="model_files_caption" class="caption"
                                                             style="position: relative; text-align: left; width: 60%; left: 0px; top: 3px;">
                                                            Для выбора файлов
                                                            нажмите на
                                                            кнопку
                                                        </div>
                                                    </div><!-- /d-popup-files-footer -->
                                                </div>

                                                <div class="modal-input-error-icon error-icon"></div>
                                                <div class="error message" style="display: none;"></div>
                                            </div>
                                            <div class="value file-name"
                                                 style="margin: 5px 0 0;padding:0;border:0"></div>
                                            <div class="modal-form-uploaded-file"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="container-agreement-model-upload-file"
                                 style="width: 250px; overflow: hidden;"></div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="value">
                            <div style="margin: 1em .5em .5em; color:#000; font-weight: bold; float: left;">Сообщение</div>
                            <textarea name="agreement_comments" style="width:100%;height:50px;resize:none; width: 245px; height: 100px;"></textarea>
                        </td>
                    </tr>

                    <?php if ($model->getStatus() != "accepted"): ?>
                        <tr>
                            <td colspan="2" class="check"
                                style="padding-top: 2em !important;border-top:1px solid #ccc !important;">
                                <label for="designer_approve">С утверждением сотрудника</label>
                                <input type="checkbox" id="designer_approve"
                                       name="designer_approve" <?php echo $model->getDesignerApprove() ? "checked" : ""; ?>
                                       data-required="false"/>
                            </td>
                        </tr>
                    <?php elseif ($model->getDesignerApprove() && $model->getStatus() == "accepted"): ?>
                        <tr>
                            <td colspan="2" class="check"
                                style="padding-top: 2em !important;border-top:1px solid #ccc !important;">
                                <label for="designer_approve">С утверждением сотрудника</label>
                                <div style='float: right; margin-right: 10px;'><img src='/images/ok-icon-active.png'
                                                                                    title=''/></div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>

                <div class="specialists-panel-container hide">
                    <div class="specialists-panel">
                        <?php include_partial('agreement_activity_model_management/panel_specialists_block', array( 'specialist_groups' => $specialist_groups )) ?>
                    </div>
                </div>

                <div class="buttons">

                    <?php if (!$model->getIsBlocked() || $model->getAllowUseBlocked()): ?>
                        <?php /*
				<?php if ($model->getStatus() != 'accepted'): ?>
					<div class="specialists button float-left modal-form-button" style="margin-bottom: 5px;"><a href="#"
																												class="specialists">Отправить
							специалистам</a></div>
				<?php endif; ?>
<?php */ ?>
                        <?php if ($model->getStatus() != 'accepted' && $model->getStatus() != "wait_specialist"): ?>
                            <?php if ($model->getModelTypeId() != 2 && $model->getModelTypeId() != 4): ?>
                                <div class="accept green button modal-form-button" style="margin: 5px;"
                                     data-model-type="model_simple"><a href="#" class="accept">Согласовать</a></div>
                            <?php else:

                                if (( $model->getStatus() == "wait" || $model->getStatus() == "wait_specialist" ) && ( $model->getStep1() == "wait" || $model->getStep1() == "none" )): ?>
                                    <div class="accept green button modal-form-button" style="margin: 5px;"
                                         data-model-type="model_scenario"><a href="#" class="accept">Согласовать
                                            сценарий</a></div>
                                <?php endif;

                                if (( $model->getStatus() == "wait" ) && $model->getStep1() == "accepted" && ( $model->getStep2() == "wait" || $model->getStep2() == "none" )): ?>
                                    <div class="accept green button modal-form-button" style="margin: 5px;"
                                         data-model-type="model_record"><a href="#" class="accept">Согласовать
                                            запись</a></div>
                                <?php endif; ?>

                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($model->getStatus() != 'declined'): ?>
                            <?php if ($model->getModelTypeId() != 2 && $model->getModelTypeId() != 4): ?>
                                <div class="decline gray button modal-form-button" style="margin: 5px;"
                                     data-model-type="model_simple"><a href="#" class="decline">Отклонить</a>
                                </div>
                            <?php else: ?>

                                <?php if ($model->getStatus() == "accepted" && $model->getStep1() == "accepted" && ( $model->getStep2() == "accepted" || $model->getStep2() == "none" )): ?>
                                    <div class="decline gray button modal-form-button" style="margin: 5px;"
                                         data-model-type="model_simple"><a href="#"
                                                                           class="decline">Отклонить</a>
                                    </div>
                                <?php endif; ?>

                                <?php if ($model->getStatus() != "accepted" && ( $model->getStep1() == "accepted" || $model->getStep1() == "wait" || $model->getStep1() == "none" )): ?>
                                    <div style='margin: 5px;' class="decline gray button  modal-form-button"
                                         data-step="first" data-model-type="model_scenario"><a href="#" class="decline">Отклонить
                                            сценарий</a></div>
                                <?php endif;

                                if ($model->getStatus() == "wait" && $model->getStep1() == "accepted" && ( $model->getStep2() == "wait" || $model->getStep2() == "none" )): ?>
                                    <div class="decline gray button  modal-form-button" style="margin: 5px;"
                                         data-step="second" data-model-type="model_record"><a href="#" class="decline">Отклонить
                                            запись</a></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div
                                style="margin: auto; text-align: center; padding-top: 20px; display: block; width: 100%; float:left;">
                            <a style="font-size: 11px; color: black;"
                               href="<?php echo url_for('@discussion_switch_to_dealer?dealer=' . $model->getDealerId() . '&activityId=' . $model->getActivityId() . '&modelId=' . $model->getId()); ?>"
                               target='_blank'>
                                Перейти в активность
                            </a>
                        </div>

                        <div class="clear"></div>
                    <?php else: ?>
                        <div class="dummy gray msg modal-form-button">Заявка заблокирована</div>

                        <div class='out-of-date' data-out='true'></div>
                        <div style="margin: auto; text-align: center; padding-top: 27px;">
                            <a style="font-size: 11px; color: black;"
                               href="<?php echo url_for('@discussion_switch_to_dealer?dealer=' . $model->getDealerId() . '&activityId=' . $model->getActivityId() . '&modelId=' . $model->getId()); ?>"
                               target='_blank'>
                                Перейти в активность
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </form>
