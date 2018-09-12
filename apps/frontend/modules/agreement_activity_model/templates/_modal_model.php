<script type="text/javascript" src="/js/form/form.js"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/form/base.js"></script>
<div id="model" class="model modal template-popup-wrap mod-popup-body">
    <div class="template-popup-bg"></div>
    <div class="template-popup-body">
        <div class="modal-close"></div>
        <div class="modal-header">
            <ul class="pages tabs model-tabs">
                <li class="tab active model-tab" data-pane="model-pane"><span>Материал</span></li>
                <li class="tab report-tab" data-pane="report-pane"><span>Отчет</span></li>
                <li class="tab discussion-tab" data-pane="discussion-pane"><span>Статус</span>
                    <div class="message">1</div>
                </li>
            </ul>
        </div>
        <div class="modal-form tab-pane model-pane" id="model-pane">
            <?php include_partial('form', array( 'activity' => $activity, 'model_types' => $model_types, 'model_types_fields' => $model_types_fields, 'model_place_fields' => $model_place_fields, 'dealer_files' => $dealer_files, 'activities' => $activities )) ?>
        </div>
        <div class="modal-form tab-pane report-pane" id="report-pane">
            <?php include_partial('agreement_activity_model_report/form', array( 'activity' => $activity )) ?>
        </div>
        <div class="tab-pane chat" id="discussion-pane">
            <?php include_partial('discussion/dealer_panel', array()) ?>
        </div>
    </div>
</div>

<script type="text/javascript" src="/js/activity/module/agreement/model/form/root_controller.js"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/form/report.js?1"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/form/model.js"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/form/discussion.js"></script>

<script type="text/javascript">
    $(function () {
        var root_controller = new AgreementModelRootControler({

            model_form: new AgreementModelForm({
                form: '#model-form',
                models_list: '#agreement-models',
                tabs_selector: '#model .tabs',
                tab_selector: '#model .model-tab',
                concept_type_id: <?php echo $concept_type ? $concept_type->getId() : 'false' ?>,
                add_url: '<?php echo url_for("@agreement_module_models_add?activity={$activity->getId()}") ?>',
                load_url: '<?php echo url_for("@agreement_module_models_edit?activity=" . $activity->getId()) ?>',
                update_url: '<?php echo url_for("@agreement_module_models_update?activity=" . $activity->getId()) ?>',
                cancel_url: '<?php echo url_for("@agreement_module_models_cancel?activity=" . $activity->getId()) ?>',
                cancel_scenario_url: '<?php echo url_for("@agreement_module_models_scenario_cancel?activity=" . $activity->getId()) ?>',
                cancel_record_url: '<?php echo url_for("@agreement_module_models_record_cancel?activity=" . $activity->getId()) ?>',
                delete_url: '<?php echo url_for('@agreement_module_models_delete?activity=' . $activity->getId()) ?>',
                load_record_block_url: '<?php echo url_for('activity_model_record_block'); ?>',
                load_model_block_url: '<?php echo url_for('activity_model_files_block'); ?>',
                dates_field_url: '<?php echo url_for('agreement_model_dates_field'); ?>',
                load_dates_and_certificates: '<?php echo url_for('agreement_dates_and_certificates'); ?>',
                delete_date_field: '<?php echo url_for('agreement_dates_delete'); ?>',
                load_concept_cert_fields_url: '<?php echo url_for('agreement_model_load_concept_cert_fields'); ?>',
                model_type_default_id: '<?php echo $model_types->getFirst()->getId(); ?>',
                init_delete_files_event: true,
                model_file_uploader: new JQueryUploader({
                    file_uploader_el: '#model_file',
                    max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                    upload_max_files_count: <?php echo sfConfig::get('app_max_files_upload_count'); ?>,
                    uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                    delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                    delete_uploaded_file_url: '<?php echo url_for('@agreement_model_delete_uploaded_file'); ?>',
                    delete_uploaded_files_list_url: '<?php echo url_for('@agreement_model_delete_uploaded_files'); ?>',
                    uploaded_files_container: '#model_files',
                    uploaded_files_caption: '#model_files_caption',
                    el_attach_files_click_bt: '#js-file-trigger-model',
                    el_attach_files_model_field: '#model_file',
                    progress_bar: '#model-files-progress-bar',
                    upload_file_object_type: 'model',
                    upload_file_type: 'model',
                    upload_field: 'model_file',
                    files_change_event: 'report_files_change',
                    scroller: '.scroller-model',
                    scroller_height: 200,
                    disabled_files_extensions: ['js'],
                    model_form: '#model-form'
                }).start(),
                concept_file_uploader: new JQueryUploader({
                    file_uploader_el: '#concept_file',
                    max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                    upload_max_files_count: <?php echo sfConfig::get('app_max_files_upload_count'); ?>,
                    uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                    delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                    delete_uploaded_file_url: '<?php echo url_for('@agreement_model_delete_uploaded_file'); ?>',
                    uploaded_files_container: '#concept_files',
                    uploaded_files_caption: '#concept_files_caption',
                    el_attach_files_click_bt: '#js-file-trigger-concept',
                    el_attach_files_model_field: '#concept_file',
                    progress_bar: '#concept-files-progress-bar',
                    upload_file_object_type: 'model',
                    upload_file_type: 'model',
                    upload_field: 'model_file',
                    files_change_event: 'report_files_change',
                    scroller: '.scroller-concept',
                    scroller_height: 200,
                    disabled_files_extensions: ['js'],
                    model_form: '#model-form'
                }).start(),
                model_record_file_uploader: new JQueryUploader({
                    file_uploader_el: '#model_record_file',
                    max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                    upload_max_files_count: <?php echo sfConfig::get('app_max_files_upload_count'); ?>,
                    uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                    delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                    delete_uploaded_file_url: '<?php echo url_for('@agreement_model_delete_uploaded_file'); ?>',
                    uploaded_files_container: '#model_record_files',
                    uploaded_files_caption: '#model_record_files_caption',
                    el_attach_files_click_bt: '#js-file-trigger-model-record',
                    el_attach_files_model_field: '#model_record_file',
                    progress_bar: '#model-record-files-progress-bar',
                    upload_file_object_type: 'model',
                    upload_file_type: 'model_record',
                    upload_field: 'model_record_file',
                    upload_files_ids_el: 'upload_files_records_ids',
                    files_change_event: 'mode_record_files_change',
                    scroller: '.scroller-model-record',
                    scroller_height: 200,
                    disabled_files_extensions: ['js'],
                    model_form: '#model-form'
                }).start(),
            }).start(),

            report_form: new AgreementModelReportForm({
                form: '#report-form',
                models_list: '#agreement-models',
                tabs_selector: '#model .tabs',
                tab_selector: '#model .report-tab',
                load_url: '<?php echo url_for("@agreement_module_models_report_edit?activity=" . $activity->getId()) ?>',
                update_url: '<?php echo url_for("@agreement_module_models_report_update?activity=" . $activity->getId()) ?>',
                cancel_url: '<?php echo url_for("@agreement_module_models_report_cancel?activity=" . $activity->getId()) ?>',
                load_additional_financial_docs_files_url: '<?php echo url_for("@agreement_module_report_load_add_fin_docs_files"); ?>',
                delete_uploaded_add_fin_doc_files_url: '<?php echo url_for("@agreement_module_report_delete_add_find_docs_file"); ?>',
                load_financial_docs_block_url: '<?php echo url_for("@agreement_module_models_report_load_block"); ?>',
                load_financial_concept_docs_block_url: '<?php echo url_for('@agreement_module_models_report_concept_load_block'); ?>',
                delete_report_file: '<?php echo url_for('@agreement_module_delete_report_file'); ?>',
                init_delete_files_event: false,
                report_file_additional_uploader: new JQueryUploader({
                    file_uploader_el: '#additional_file',
                    max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                    upload_max_files_count: <?php echo sfConfig::get('app_max_files_upload_count'); ?>,
                    uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                    delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                    delete_uploaded_file_url: '<?php echo url_for('@agreement_model_delete_uploaded_file'); ?>',
                    uploaded_files_container: '#report_additional_files',
                    uploaded_files_caption: '#report_additional_files_caption',
                    el_attach_files_click_bt: '#js-file-trigger-report-additional-file',
                    el_attach_files_model_field: '#additional_file',
                    progress_bar: '#report-additional-files-progress-bar',
                    upload_file_object_type: 'report',
                    upload_file_type: 'report_additional',
                    upload_field: 'additional_file',
                    upload_files_ids_el: 'upload_files_additional_ids',
                    files_change_event: 'report_files_change',
                    scroller: '.scroller-add-docs',
                    disabled_files_extensions: ['js'],
                    model_form: '#report-form',
                    add_caption_br_tag: true
                }).start(),
                report_file_financial_uploader: new JQueryUploader({
                    file_uploader_el: '#financial_docs_file',
                    max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                    upload_max_files_count: <?php echo sfConfig::get('app_max_files_upload_count'); ?>,
                    uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                    delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                    delete_uploaded_file_url: '<?php echo url_for('@agreement_model_delete_uploaded_file'); ?>',
                    uploaded_files_container: '#report_financial_files',
                    uploaded_files_caption: '#report_financial_files_caption',
                    el_attach_files_click_bt: '#js-file-trigger-report-financial-file',
                    el_attach_files_model_field: '#financial_docs_file',
                    progress_bar: '#report-financial-files-progress-bar',
                    upload_file_object_type: 'report',
                    upload_file_type: 'report_financial',
                    upload_field: 'financial_docs_file',
                    upload_files_ids_el: 'upload_files_financial_ids',
                    files_change_event: 'report_files_change',
                    scroller: '.scroller-add-fin',
                    disabled_files_extensions: ['js'],
                    model_form: '#report-form',
                    add_caption_br_tag: true
                }).start(),
                concept_file_uploader: new JQueryUploader({
                    file_uploader_el: '#concept_report_file',
                    max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                    upload_max_files_count: <?php echo sfConfig::get('app_max_files_upload_count'); ?>,
                    uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                    delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                    delete_uploaded_file_url: '<?php echo url_for('@agreement_model_delete_uploaded_file'); ?>',
                    uploaded_files_container: '#concept_report_files',
                    uploaded_files_caption: '#concept_report_files_caption',
                    el_attach_files_click_bt: '#js-file-trigger-concept-report',
                    el_attach_files_model_field: '#concept_report_file',
                    progress_bar: '#concept-report-files-progress-bar',
                    upload_file_object_type: 'report',
                    upload_file_type: 'report_financial',
                    upload_field: 'concept_report_file',
                    upload_files_ids_el: 'upload_files_financial_ids',
                    files_change_event: 'report_files_change',
                    scroller: '.scroller-concept-report',
                    scroller_height: 200,
                    disabled_files_extensions: ['js'],
                    model_form: '#report-form',
                    add_caption_br_tag: true
                }).start(),
            }).start(),

            discussion_controller: new AgreementModelDiscussionController({
                models_list: '#agreement-models',
                tabs_selector: '#model .tabs',
                tab_selector: '#model .discussion-tab',
                panel_selector: '#discussion-pane',
                state_url: "<?php echo url_for('@discussion_state') ?>",
                new_messages_url: "<?php echo url_for('@discussion_new_messages') ?>",
                post_url: "<?php echo url_for('@discussion_post') ?>",
                previous_url: "<?php echo url_for('@discussion_previous') ?>",
                search_url: "<?php echo url_for('@discussion_search') ?>",
                online_check_url: "<?php echo url_for('@discussion_online_check') ?>",
                session_name: '<?php echo session_name() ?>',
                session_id: '<?php echo session_id() ?>',
                delete_file_url: "<?php echo url_for('@upload_temp_delete') ?>",
                load_chat_last_messages_and_files: "<?php echo url_for('@agreement_model_discussion_load_chat_last_messages_and_files'); ?>",
                scroller: '.scroller-discussion-uploaded-files',
                scroller_discussion: '.scroller-discussion-messages',
                discussion_file_uploader: new JQueryUploader({
                    file_uploader_el: '#discussion_comment_file',
                    max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                    uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                    delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                    delete_uploaded_file_url: '<?php echo url_for('@agreement_model_delete_uploaded_file'); ?>',
                    uploaded_files_container: '#discussion_files',
                    el_attach_files_model_field: '#discussion_comment_file',
                    progress_bar: '#discussion-files-progress-bar',
                    upload_files_ids_el: 'upload_files_discussion_ids',
                    upload_file_object_type: 'discussion',
                    upload_file_type: 'discussion',
                    upload_field: 'discussion_comment_file',
                    draw_only_labels: true,
                    el_attach_files_click_bt: '#btn-add-discussion-dealer-files',
                    disabled_files_extensions: ['js'],
                    model_form: '#discussion_upload_form'
                }).start()
            }).start(),

            modal: '#model',
            add_model_button: '#add-model-button',
            model_row: '.model-row',
            concept_row: '.concept-row',
            list_selector: '#agreement-models',
            sort_url: '<?php echo url_for('@agreement_module_models_sort?activity=' . $activity->getId()) ?>',
            add_many_concepts_url: '<?php echo url_for('@add_many_concepts'); ?>',
            btn_add_new_concept: '#add-model-concept-button'
        }).start();

        window.agreement_model_form = root_controller.model_form;
        window.agreement_model_report_form = root_controller.report_form;

        window.discussion_comments_form = root_controller.discussion_controller.getDiscussion();
    });
</script>
