<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 12.07.2016
 * Time: 11:59
 */
?>
<div class="approvement" style="display: inline-table; width: 100%;">
    <h1>Сообщения</h1>

    <hr/>
    <div class="actions-wrapper">
        <div class="activity-main-page">
            <!-- Nav tabs -->
            <div id="tabs-messages" class="tabs-activity">
                <ul class="nav nav-tabs">
                    <li data-tab="<?php echo Discussion::PAGER_NEW_MESSAGES; ?>" class="messages-tab-header <?php echo $tab == Discussion::PAGER_NEW_MESSAGES ? "active" : ""; ?>">
                        <a href="javascript:;" name="<?php echo Discussion::PAGER_NEW_MESSAGES; ?>" class="tabHeader">Новые</a>
                    </li>
                    <li data-tab="<?php echo Discussion::PAGER_READED_MESSAGES; ?>" class="messages-tab-header <?php echo $tab == Discussion::PAGER_READED_MESSAGES ? "active" : ""; ?>">
                        <a href="javascript:;" name="<?php echo Discussion::PAGER_READED_MESSAGES; ?>" class="tabHeader">Прочитанные</a>
                    </li>
                </ul>
            </div>

            <div class="tabs-activity" style="position: absolute; right: 10px; z-index: 999; top: 47px;">
                <ul class="nav nav-tabs">
                    <li><a href="javascript:;" class="messages-types messages-from-models" data-type="models" data-messages-parent="<?php echo Discussion::PAGER_NEW_MESSAGES; ?>">Сообщения по заявкам</a></li>
                    <li><a href="javascript:;" class="messages-types messages-from-form" data-type="form" data-messages-parent="<?php echo Discussion::PAGER_NEW_MESSAGES; ?>">Сообщения через форму</a></li>
                </ul>
            </div>

            <div id="loading-progress-container" style="margin: 10px; text-align: center; display: none;"><img src="/images/progress-mini.gif"></div>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="<?php echo Discussion::PAGER_NEW_MESSAGES; ?>">
                    <?php include_partial('messages_list', array('pager' => $pager_new, 'paginatorData' => $pager_new_paginatorData, 'page_parent' => $page_parent)); ?>
                </div>

                <div class="tab-pane" id="<?php echo Discussion::PAGER_READED_MESSAGES; ?>">
                    <?php include_partial('messages_list', array('pager' => $pager_readed, 'paginatorData' => $pager_readed_paginatorData)); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<iframe style="position: absolute;" src="/blank.html" width="1" height="1" frameborder="0" hspace="0"
        marginheight="0"
        marginwidth="0" name="agreement-model-comments-frame" scrolling="no"></iframe>

<script type="text/javascript" src="/js/activity/module/agreement/model/management/model/root_controller.js"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/management/model/model_controller.js"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/management/model/specialists_form.js"></script>
<script type="text/javascript" src="/js/activity/module/agreement/model/form/discussion.js"></script>

<script>
    $(function () {
         window.special_discussion_form = new SpecialDiscussion({
            post_url: "<?php echo url_for('@discussion_special_message_add') ?>",
            panel: ".panel-special-message",
            uploader: new Uploader({
                selector: '#special-modal .message-upload',
                session_name: '<?php echo session_name() ?>',
                session_id: '<?php echo session_id() ?>',
                upload_url: '/upload.php',
                delete_url: "<?php echo url_for('@upload_temp_delete') ?>"
            }).start(),
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
        }).start();

        window.message_tabs = new MessagesTabs({
            load_url: "<?php echo url_for('@discussion_messages'); ?>",
            on_get_messages_list_by_type: "<?php echo url_for('@on_get_messages_list_by_type') ; ?>",
            limit: parseInt("<?php echo sfConfig::get('app_max_items_on_page'); ?>")
        }).start();
    });
</script>
