<div id="model" class="model modal hide mod-popup-body d-popup-wrap" style="width: 900px;">
    <div class="d-popup-body">
        <div class="modal-header">
            <ul class="pages tabs model-tabs">
                <li class="tab active model-tab" data-pane="model-pane"><span>Материал</span></li>
                <li class="tab report-tab" data-pane="report-pane"><span>Отчет</span></li>
                <li class="tab discussion-tab" data-pane="discussion-pane"><span>Статус</span>
                    <div class="message">1</div>
                </li>
            </ul>
        </div>
        <div class="modal-close"></div>
        <div class="modal-form model-pane" id="model-pane">
            <?php //include_partial('panel_agreement', array('panel_id' => 'model-panel')) ?>
            <div id="model-panel"><div class="values"></div></div>
        </div>
        <div class="modal-form report-pane" id="report-pane">
            <?php //include_partial('panel_agreement', array('panel_id' => 'report-panel')) ?>
            <div id="report-panel"><div class="values"></div></div>
        </div>
        <div class="tab-pane chat" id="discussion-pane">
            <?php include_partial('discussion/manager_panel') ?>
        </div>
    </div></div>

<script type="text/javascript" src="/js/activity/module/agreement/model/specialist/model/root_controller.js"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/specialist/model/model_controller.js"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/form/discussion.js"></script>
<script type="text/javascript">
    $(function () {
        var controller = new AgreementModelSpecialistRootController({
            modal_selector: '#model',
            list_selector: '#agreement-models',
            model_controller: new AgreementModelSpecialistController({
                selector: '#model-panel',
                decline_type: 'decline_model',
                load_url: '<?php echo url_for('@agreement_module_specialist_models_view_model') ?>',
                decline_url: '<?php echo url_for('@agreement_module_specialist_models_decline_model') ?>',
                accept_url: '<?php echo url_for('@agreement_module_specialist_models_accept_model') ?>',
                accept_decline_url: '<?php echo url_for('@agreement_module_specialist_models_accept_model') ?>',
                max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                panel_type: 'model'
            }).start(),

            report_controller: new AgreementModelSpecialistController({
                selector: '#report-panel',
                decline_type: 'decline_report',
                load_url: '<?php echo url_for('@agreement_module_specialist_models_view_report') ?>',
                decline_url: '<?php echo url_for('@agreement_module_specialist_models_decline_report') ?>',
                accept_url: '<?php echo url_for('@agreement_module_specialist_models_accept_report') ?>',
                accept_decline_url: '<?php echo url_for('@agreement_module_specialist_models_accept_report') ?>',
                max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                panel_type: 'report'
            }).start(),

            discussion_controller: new AgreementModelDiscussionController({
                models_list: '#agreement-models',
                tabs_selector: '#model .model-tabs',
                tab_selector: '#model .discussion-tab',
                panel_selector: '#discussion-pane',
                model_row: '.model-row',
                state_url: "<?php echo url_for('@discussion_state') ?>",
                new_messages_url: "<?php echo url_for('@discussion_new_messages') ?>",
                post_url: "<?php echo url_for('@discussion_post') ?>",
                previous_url: "<?php echo url_for('@discussion_previous') ?>",
                search_url: "<?php echo url_for('@discussion_search') ?>",
                online_check_url: "<?php echo url_for('@discussion_online_check') ?>",
                session_name: '<?php echo session_name() ?>',
                session_id: '<?php echo session_id() ?>',
                delete_file_url: "<?php echo url_for('@upload_temp_delete') ?>",
                load_chat_last_messages_and_files: "<?php echo url_for('@agreement_model_discussion_load_chat_last_messages_and_dealer_files'); ?>",
                discussion_file_uploader: new JQueryUploader({
                    file_uploader_el: '#discussion_agreement_comment_file',
                    max_file_size: '<?php echo sfConfig::get('app_max_upload_size'); ?>',
                    uploader_url: '<?php echo '/upload_ajax.php'; ?>',
                    delete_temp_file_url: '<?php echo url_for('@upload_temp_ajax_delete'); ?>',
                    delete_uploaded_file_url: '<?php echo url_for('@agreement_model_delete_uploaded_file'); ?>',
                    uploaded_files_container: '#discussion_agreement_files',
                    el_attach_files_model_field: '#discussion_agreement_comment_file',
                    progress_bar: '#discussion-agreement-files-progress-bar',
                    upload_files_ids_el: 'upload_files_discussion_agreement_ids',
                    upload_file_object_type: 'discussion',
                    upload_file_type: 'discussion',
                    upload_field: 'discussion_agreement_comment_file',
                    draw_only_labels: true,
                    el_attach_files_click_bt: '#btn-add-discussion-agreement-dealer-files',
                    model_form: '#discussion_upload_form'
                }).start()
            }).start()
        }).start();

        window.accept_decline_form = controller.model_controller.getAcceptDeclineForm();
        window.accept_report_form = controller.report_controller.getAcceptDeclineForm();

        window.decline_model_form = controller.model_controller.getAcceptDeclineForm();
        window.decline_report_form = controller.report_controller.getAcceptDeclineForm();
    });
</script>

<iframe src="/blank.html" width="1" height="1" frameborder="0" hspace="0" marginheight="0" marginwidth="0"
        name="accept-frame" scrolling="no"></iframe>
<iframe src="/blank.html" width="1" height="1" frameborder="0" hspace="0" marginheight="0" marginwidth="0"
        name="decline-frame" scrolling="no"></iframe>

