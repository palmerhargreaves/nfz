<?php
if ($report):
$model_type = $report->getModel()->getModelType();
$model = $report->getModel();
?>

<?php if (Utils::allowedIps()): ?>
<form id="agreement-model-report-form" method="post" action="/" enctype="multipart/form-data"
      target="accept-decline-report-frame">
    <?php else: ?>
    <form id="agreement-model-report-form" method="post" action="/" enctype="multipart/form-data"
          target="accept-decline-report-frame">
        <?php endif; ?>

        <input type="hidden" name="id" value="<?php echo $model->getId(); ?>"/>
        <input type="hidden" name="action_type" value=""/>

        <input type="hidden" name="<?php echo session_name(); ?>" value="<?php echo session_id(); ?>">
        <input type="hidden" name="upload_file_object_type" value=""/>
        <input type="hidden" name="upload_file_type" value=""/>
        <input type="hidden" name="upload_field" value=""/>
        <input type="hidden" name="upload_files_ids" value=""/>

        <div class="model-data"
             data-model-status="<?php echo $model->getStatus() ?>"
             data-css-status="<?php echo $model->getReportCssStatus() ?>"
             data-is-concept="<?php echo $model->isConcept() ? 'true' : 'false' ?>"/>

        <div class="template-popup-form">
            <div class="template-popup-form-l">
                <div class="d-popup-files-wrap scrollbar-inner">
                    <div class="d-popup-files-row">
                        <?php $idx = 1; ?>
                        <?php foreach ($report->getUploadedFilesSchemaByType() as $model_files_type => $model_type): ?>
                            <?php if ($model_type['show']): ?>
                                <div style="margin-top: 15px;">
                                    <?php $header = $model_type['label']; ?>
                                    <label><?php echo $header; ?></label>

                                    <div class="scroller scroller-report-files-<?php echo $idx++; ?>"
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
                                                <div class="d-popup-uploaded-files d-cb" style="min-height: 135px;"
                                                     data-toggled="toggle-view-box-<?php echo $normalized_header; ?>">
                                                    <?php foreach (ModelReportFiles::getModelFilesTypes() as $f_type): ?>
                                                        <?php
                                                        ModelReportFiles::sortFileList(function ($files_list) use ($report, $f_type, $model_type, $model_files_type) {
                                                            include_partial('agreement_activity_model_report/report_uploaded_files/_report_' . $f_type . '_file',
                                                                array
                                                                (
                                                                    'files_list' => $files_list,
                                                                    'report' => $report,
                                                                    'allow_remove' => false,
                                                                    'allow_add_file_to_favorites' => false,
                                                                    'by_type' => $model_files_type
                                                                )
                                                            );
                                                        },
                                                            $model,
                                                            $model_type['type'],
                                                            $model_files_type,
                                                            $f_type
                                                        );
                                                        ?>
                                                    <?php endforeach; ?>

                                                    <div class="d-popup-files-footer d-cb">

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


            <div class="template-popup-form-r">
                <div class="d-popup-req-title">
                    <strong>Файл</strong>
                </div>

                <div class="file">
                    <div class="modal-file-wrapper input">
                        <div id="report-files-progress-bar"
                             class="progress-bar-content progress-bar-full-width"></div>
                    </div>
                </div>

                <div class="scroller scroller-report" style="margin-bottom: 10px; height: 210px;">
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

                                    <div id="container_model_files" class="control dropzone"
                                         style="width: auto !important;">
                                        <div class="d-popup-files-wrap scrollbar-inner">
                                            <div class="d-popup-files-row">
                                                <div id="model_report_files"
                                                     class="d-popup-uploaded-files d-cb"></div>
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
                                                 style="position: relative; text-align: left; left: 0px; top: 1px; width: 60%;">
                                                Для
                                                выбора файлов
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

                <table>
                    <tr>
                        <td colspan="2" class="value">
                            <div style="margin: 1em .5em .5em; color:#000; font-weight: bold; float: left;">Сообщение</div>
                            <textarea name="agreement_comments" style="width:100%;height:50px;resize:none; width: 245px; height: 100px;"></textarea>
                        </td>
                    </tr>

                </table>

                <table>
                    <?php if ($model->getStatus() == "accepted" && $report->getStatus() != "accepted"): ?>
                        <tr>
                            <td colspan="2" class="check"
                                style="padding-top: 2em !important;border-top:1px solid #ccc !important;">
                                <label for="designer_approve">С утверждением сотрудника</label>
                                <input type="checkbox" id="designer_approve"
                                       name="designer_approve" <?php echo $model->getDesignerApprove() ? "checked" : ""; ?>
                                       data-required="false"/>
                            </td>
                        </tr>
                    <?php elseif ($model->getDesignerApprove()): ?>
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
                        <?php include_partial('agreement_activity_model_management/panel_specialists_block', array('specialist_groups' => $specialist_groups)) ?>
                    </div>
                </div>

                <div class="buttons">
                    <?php if (!$report->getModel()->getIsBlocked() || $report->getModel()->getAllowUseBlocked()): ?>
                        <?php if ($report->getStatus() != 'not_sent'): ?>
                            <?php if (!$report->getModel()->isOutOfDate()): ?>
                                <?php if ($report->getStatus() != 'accepted'): ?>
                                    <!--<div class="specialists button float-left modal-form-button"><a href="#" class="specialists">Отправить
                                            специалистам</a></div>-->
                                <?php endif; ?>

                                <?php if ($report->getStatus() != 'accepted'): ?>
                                    <div class="accept green button float-left modal-form-button"
                                         data-model-type="report_accept"><a href="#" class="accept">Согласовать</a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($report->getStatus() != 'declined'): ?>
                                    <div class="decline gray button float-right modal-form-button"
                                         data-model-type="report_decline"><a href="#" class="decline">Отклонить</a>
                                    </div>
                                <?php endif; ?>
                                <div class="clear"></div>
                            <?php endif;
                        endif;
                        ?>
                    <?php else: ?>
                        <div class="dummy gray msg modal-form-button">Заявка заблокирована</div>

                        <div class='out-of-date' data-out='true'></div>
                        <div style="margin: auto; text-align: center; padding-top: 27px;">
                            <a style="font-size: 11px; color: black;"
                               href="<?php echo url_for('@discussion_switch_to_dealer?dealer=' . $report->getModel()->getDealerId() . '&activityId=' . $report->getModel()->getActivityId() . '&modelId=' . $report->getModel()->getId()); ?>"
                               target='_blank'>
                                Перейти в активность
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div style="text-align:center;">Отчёт не загружен</div>

            <?php
            $blocked_to = strtotime($model->getUseBlockedTo());
            if (!empty($blocked_to) && strtotime(date('d-m-Y H:i:s')) < $blocked_to): ?>
                <div class="msg" style="background: #c4e6c8; width: 100%; margin: 10px;">Заявка разблокирована
                    до: <?php echo date('d-m-Y H:i:s', $blocked_to); ?></div>
            <?php elseif ($model->getIsBlocked() && !$model->getAllowUseBlocked()): ?>
                <div class="dummy gray msg modal-form-button" style="margin-top: 10px;">Заявка заблокирована</div>
            <?php endif; ?>
        <?php endif; ?>
    </form>
