<?php if ( Utils::allowedIps() ): ?>
<form action="<?php echo url_for("@agreement_module_models_report_update?activity={$activity->getId()}") ?>"
      class="form-horizontal" method="post" enctype="multipart/form-data" id="report-form" target="report-target">
    <?php else: ?>
    <form action="<?php echo url_for("@agreement_module_models_report_update?activity={$activity->getId()}") ?>"
          class="form-horizontal" method="post" enctype="multipart/form-data" id="report-form" target="report-target">
        <?php endif; ?>


        <input type="hidden" name="id"/>
        <div class="d-popup-cols concept-form">
            <p class="description">Загрузите сюда отчет по результатам проведения данной акции в вашем дилерском
                центре.</p>
            <div class="requirements">
                Отчет должен содержать полную информацию об активности вашего дилерского центра в рамках проведениях
                данной
                акции и оценку эффективности проведенной кампании.<br/><br/>
                <strong>Внимание! Отчет по итогам акции загружается после ее проведения и согласования всех рекламных
                    материалов.</strong>
                <br>
                Максимальный размер файла <?php echo F::getSmartSize(sfConfig::get('app_max_upload_size'), 0) ?>.
            </div>

            <div class="d-popup-files-wrap scrollbar-inner">
                <div class="d-popup-files-row">
                    <div class="file">
                        <div class="modal-file-wrapper input">
                            <div id="concept-report-files-progress-bar"
                                 class="progress-bar-content progress-bar-full-width"></div>
                        </div>
                    </div>

                    <div class="scroller scroller-add-fin">
                        <div class="scrollbar">
                            <div class="track">
                                <div class="thumb">
                                    <div class="end"></div>
                                </div>
                            </div>
                        </div>
                        <div class="viewport scroller-wrapper">
                            <div class="overview scroller-inner">

                                <div class="file">
                                    <div class="modal-file-wrapper input">
                                        <div id="container_concept_report_files" class="control dropzone"
                                             style="min-height: 294px">
                                            <div class="d-popup-files-wrap scrollbar-inner">
                                                <div class="d-popup-files-row">
                                                    <div id="concept_report_files"
                                                         class="d-popup-uploaded-files d-cb"></div>
                                                </div>
                                            </div>

                                            <div class="caption_n">Для выбора файлов нажмите на
                                                кнопку или
                                                перетащите их сюда
                                            </div>
                                            <input type="file" name="concept_report_file" id="concept_report_file"
                                                   multiple/>
                                        </div>
                                        <div class="modal-input-error-icon error-icon"></div>
                                        <div class="error message"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="d-popup-uploaded-files d-cb" style="padding: 1px; height: 50px; min-height: 50px;">
                        <div class="d-popup-files-footer d-cb">
                            <a href="javascript:" id="js-file-trigger-concept-report"
                               class="button js-d-popup-file-trigger" data-target="concept_report_file">Прикрепить
                                файл</a>

                            <span id="concept_report_files_caption">
                            Прикреплено - 0 файлов.<br/>
                            Общий размер - 0 МБ
                        </span>
                        </div><!-- /d-popup-files-footer -->
                    </div><!-- /d-popup-uploaded-files -->
                </div>
            </div>

            <div class="mod-popup-tab-footer">
                <div class="mod-popup-buttons">
                    <input type="submit" name="" class="modal-form-submit-button submit-btn"
                           value="Отправить на согласование"/>

                    <div class="margin-auto gray button cancel cancel-btn">Отменить отправку</div>
                    <div class="dummy gray msg modal-form-button report-blocked-info" style="display: none;">Заявка
                        заблокирована
                    </div>
                </div>

            </div><!-- /tab-report-footer -->
        </div>

        <div class="model-form" style="width: 100%; display: inline-block;">
            <!--Left block-->
            <div class="d-popup-cols">
                <div class="d-popup-col">
                    <div class="d-popup-files-wrap scrollbar-inner">
                        <div class="d-popup-files-row">
                            <label>Фотоотчет</label>

                            <div class="file">
                                <div class="modal-file-wrapper input">
                                    <div id="report-additional-files-progress-bar"
                                         class="progress-bar-content progress-bar-full-width"></div>
                                </div>
                            </div>

                            <div class="scroller scroller-add-docs">
                                <div class="scrollbar">
                                    <div class="track">
                                        <div class="thumb">
                                            <div class="end"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="viewport scroller-wrapper">
                                    <div class="overview scroller-inner">

                                        <div class="file">
                                            <div class="modal-file-wrapper input">
                                                <div id="container_model_files" class="control dropzone"
                                                     style="min-height: 294px">
                                                    <div class="d-popup-files-wrap scrollbar-inner">
                                                        <div class="d-popup-files-row">
                                                            <div id="report_additional_files"
                                                                 class="d-popup-uploaded-files d-cb"></div>
                                                        </div>
                                                    </div>

                                                    <div id="model_files_caption" class="caption_n">Для выбора файлов
                                                        нажмите на
                                                        кнопку или перетащите
                                                        их сюда
                                                    </div>

                                                    <input type="file" name="additional_file" id="additional_file"
                                                           multiple/>
                                                </div>
                                                <div class="modal-input-error-icon error-icon"></div>
                                                <div class="error message"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-popup-uploaded-files d-cb"
                                 style="padding: 1px; height: 50px; min-height: 50px;">
                                <div class="d-popup-files-footer d-cb">

                                    <a href="javascript:" id="js-file-trigger-report-additional-file"
                                       class="button js-d-popup-file-trigger" data-target="additional_file">Прикрепить
                                        файл</a>
                                    <span id="report_additional_files_caption">
                                    Прикреплено - 0 файлов.<br/>
                                    Общий размер - 0 МБ
                                </span>
                                </div><!-- /d-popup-files-footer -->
                            </div><!-- /d-popup-uploaded-files -->
                        </div>
                    </div>

                    <div class="fld-title additional-files-to-upload-with-places"
                         data-places-to-upload-orig="0"
                         style="display: none; float: left; color: #e60c00; font-size: 12px; margin-top: 20px; text-align: center; font-weight: bold; width: 93%;"></div>
                </div>

                <div class="d-popup-col">
                    <div class="d-popup-files-wrap scrollbar-inner financial-file">
                        <div class="d-popup-files-row">
                            <label>Финансовые документы</label>

                            <div class="file">
                                <div class="modal-file-wrapper input">
                                    <div id="report-financial-files-progress-bar"
                                         class="progress-bar-content progress-bar-full-width"></div>
                                </div>
                            </div>

                            <div class="scroller scroller-add-fin">
                                <div class="scrollbar">
                                    <div class="track">
                                        <div class="thumb">
                                            <div class="end"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="viewport scroller-wrapper">
                                    <div class="overview scroller-inner">

                                        <div class="file">
                                            <div class="modal-file-wrapper input">
                                                <div id="container_model_files" class="control dropzone"
                                                     style="min-height: 294px">
                                                    <div class="d-popup-files-wrap scrollbar-inner">
                                                        <div class="d-popup-files-row">
                                                            <div id="report_financial_files"
                                                                 class="d-popup-uploaded-files d-cb"></div>
                                                        </div>
                                                    </div>

                                                    <div id="model_files_caption" class="caption_n">Для выбора файлов
                                                        нажмите на
                                                        кнопку или
                                                        перетащите их сюда
                                                    </div>
                                                    <input type="file" name="financial_docs_file"
                                                           id="financial_docs_file"
                                                           multiple/>
                                                </div>
                                                <div class="modal-input-error-icon error-icon"></div>
                                                <div class="error message"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="d-popup-uploaded-files d-cb"
                                 style="padding: 1px; height: 50px; min-height: 50px;">
                                <div class="d-popup-files-footer d-cb">
                                    <a href="javascript:" id="js-file-trigger-report-financial-file"
                                       class="button js-d-popup-file-trigger" data-target="financial_docs_file">Прикрепить
                                        файл</a>

                                    <span id="report_financial_files_caption">
                                Прикреплено - 0 файлов.<br/>
                                Общий размер - 0 МБ
                            </span>
                                </div><!-- /d-popup-files-footer -->
                            </div><!-- /d-popup-uploaded-files -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="mod-popup-tab-footer">

                <div class="d-table cost">
                    <label class="d-cell">Сумма без НДС, руб:</label>
                    <div class="d-cell">
                        <input type="text" value="" name="cost" placeholder="0 руб."
                               data-format-expression="^[0-9]+(\.[0-9]+)?$" data-required="true"
                               data-right-format="100.00">
                        <div class="modal-input-error-icon error-icon"></div>
                        <div class="error message"></div>
                        <div class="value"></div>
                    </div>
                </div>

                <div class="mod-popup-buttons">
                    <input type="submit" name="" class="modal-form-submit-button submit-btn"
                           value="Отправить на согласование"/>

                    <div class="margin-auto gray button cancel cancel-btn">Отменить отправку</div>
                    <div class="dummy gray msg modal-form-button report-blocked-info" style="display: none;">Заявка
                        заблокирована
                    </div>
                </div>

            </div><!-- /tab-report-footer -->

        </div>

        <input type="hidden" name="<?php echo session_name(); ?>" value="<?php echo session_id(); ?>">
        <input type="hidden" name="upload_file_object_type" value=""/>
        <input type="hidden" name="upload_file_type" value=""/>
        <input type="hidden" name="upload_field" value=""/>
        <input type="hidden" name="upload_files_additional_ids" value=""/>
        <input type="hidden" name="upload_files_financial_ids" value=""/>
    </form>

    <iframe src="/blank.html" width="1" height="1" frameborder="0" hspace="0" marginheight="0" marginwidth="0"
            name="report-target" scrolling="no"></iframe>
