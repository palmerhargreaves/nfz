<?php $totalFiles = sfConfig::get('app_max_files_upload_count'); ?>
<?php
if ($report):
    $model_type = $report->getModel()->getModelType();
    ?>

    <form action="/" method="post" enctype="multipart/form-data" target="decline-frame" id="agreement-model-report-specialist-form">
        <input type="hidden" name="id"/>
        <input type="hidden" name="action_type"/>

        <input type="hidden" name="<?php echo session_name(); ?>" value="<?php echo session_id(); ?>">
        <input type="hidden" name="upload_file_object_type" value=""/>
        <input type="hidden" name="upload_file_type" value=""/>
        <input type="hidden" name="upload_field" value=""/>
        <input type="hidden" name="upload_files_ids" value=""/>

        <div class="d-popup-cols">
            <div class="d-popup-col">
                <div class="d-popup-files-wrap scrollbar-inner">
                    <div class="d-popup-files-row">

                        <?php
                        $label = '';
                        if ($model_type->hasAdditionalFile()) {
                            $label = $model_type->getReportFieldDescription();
                        }
                        ?>
                        <?php $idx = 1; ?>
                        <?php foreach ($report->getUploadedFilesSchemaByType(!empty($label) ? array($label) : '') as $model_files_type => $model_type): ?>
                            <?php if ($model_type['show']): ?>
                                <div style="margin-top: 15px;">
                                    <?php $header = $model_type['label']; ?>
                                    <label><?php echo $header; ?></label>

                                    <div class="scroller scroller-specialist-report-files-<?php echo $idx++; ?>"
                                         style="margin-bottom: 10px; height: 200px;">
                                        <div class="scrollbar" style="height: 200px;">
                                            <div class="track" style="height: 200px;">
                                                <div class="thumb">
                                                    <div class="end"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="viewport scroller-wrapper" style="height: 200px;">
                                            <div class="overview scroller-inner">

                                                <?php $normalized_header = Utils::normalize($header); ?>
                                                <div class="d-popup-uploaded-files d-cb"
                                                     data-toggled="toggle-view-box-<?php echo $normalized_header; ?>">
                                                    <?php foreach (ModelReportFiles::getModelFilesTypes() as $f_type): ?>
                                                        <?php
                                                        ModelReportFiles::sortFileList(function ($files_list) use ($report, $f_type) {
                                                            include_partial('agreement_activity_model_report/report_uploaded_files/_report_' . $f_type . '_file',
                                                                array
                                                                (
                                                                    'files_list' => $files_list,
                                                                    'report' => $report,
                                                                    'allow_remove' => false,
                                                                    'by_type' => $f_type
                                                                )
                                                            );
                                                        },
                                                            $report->getModel(),
                                                            $model_type['type'],
                                                            $model_files_type,
                                                            $f_type
                                                        );
                                                        ?>
                                                    <?php endforeach; ?>

                                                    <div class="d-popup-files-footer d-cb">
                                                        <a href="<?php echo url_for('@agreement_model_report_download_all_uploaded_files?id=' . $report->getId() . '&model_file_type=' . $model_files_type); ?>"
                                                           class="lnk-download">Скачать все</a>
                                                    </div><!-- /d-popup-files-footer -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-popup-col">
                <div class="d-popup-req-title">
                    <strong>Файл</strong>
                </div>

                <div class="file">
                    <div class="modal-file-wrapper input">
                        <div id="report-files-progress-bar" class="progress-bar-content progress-bar-full-width"></div>
                    </div>
                </div>

                <div class="scroller scroller-report" style="margin-bottom: 10px; height: 200px;">
                    <div class="scrollbar">
                        <div class="track">
                            <div class="thumb">
                                <div class="end"></div>
                            </div>
                        </div>
                    </div>
                    <div class="viewport scroller-wrapper" style="height: 200px;">
                        <div class="overview scroller-inner">
                            <div class="file">
                                <div class="modal-file-wrapper input">
                                    <div id="container_model_files" class="control dropzone">
                                        <div class="d-popup-files-wrap scrollbar-inner">
                                            <div class="d-popup-files-row">
                                                <div id="model_report_files" class="d-popup-uploaded-files d-cb"></div>
                                                <input type="file" id="agreement_report_comments_file"
                                                       name="agreement_comments_file"
                                                       multiple>
                                            </div>
                                        </div>

                                        <div class="d-popup-files-footer d-cb">
                                            <a href="javascript:" id="js-file-trigger-model-report"
                                               class="button js-d-popup-file-trigger"
                                               data-target="#agreement_report_comments_file">Прикрепить файл</a>
                                            <div id="model_report_files_caption" class="caption"
                                                 style="position: relative; text-align: left; width: 60%;">Для выбора
                                                файлов
                                                нажмите
                                                на
                                                кнопку
                                            </div>
                                        </div><!-- /d-popup-files-footer -->

                                    </div>

                                    <div class="modal-input-error-icon error-icon"></div>
                                    <div class="error message" style="display: none;"></div>
                                </div>
                                <div class="value file-name" style="margin: 5px 0 0;padding:0;border:0"></div>
                                <div class="modal-form-uploaded-file"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-agreement-model-upload-file" style="width: 250px; overflow: hidden;"></div>
                <div class="d-popup-message-wrap" style="margin-top: 15px;">
                    <label>Сообщение</label>
                    <textarea name="agreement_comments" style="width: 100%;"></textarea>
                </div>

                <div class="buttons" style="width: 350px;">
                    <?php $comment = $report->getSpecialistComment($sf_user->getAuthUser()->getRawValue()); ?>
                    <?php if ($comment && $comment->getStatus() == 'wait'): ?>
                        <div style="width: 115px;" class="accept button float-left modal-form-button"><a href="#"
                                                                                                         class="accept">Согласовать</a>
                        </div>
                        <div style="width: 115px;" class="decline gray button float-right modal-form-button"><a href="#"
                                                                                                                class="decline">Отклонить</a>
                        </div>
                    <?php endif; ?>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </form>
<?php else: ?>
    Отчёт не загружен
<?php endif; ?>
