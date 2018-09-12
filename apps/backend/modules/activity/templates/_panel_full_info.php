<div class="modal hide fade full-info-modal" id="full-info-modal" style="width: 835px; left: 45%;">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Информация</h3>
  </div>
  <div class="modal-body">
    <div class="panel-info-left fields-list" style="width: 35%; float:left;"></div>
    <div style="float:right; width: 62%; text-align: center;">
      <div class='inputs-fields panel-info-content'>Нет данных. Для добавления полей выберите тип поля.</div>
    </div>
  </div>
  <div class="modal-footer">
    <a href='#' class='btn action-activity-config-info-params-save-fields' data-field-type='information' style="float: left; display: none;">Сохранить</a>
    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
  </div>
</div>
<script type="text/javascript">
$(function() {
  /*window.tasks_acceptor = new TasksAcceptor({
    modal: '#accept-task-modal',
    tasks_url: '<?php echo url_for('@dealer_tasks') ?>'
  }).start();*/

  window.activity_full_info = new Info({
    modal : '#full-info-modal',
    load_fields_url : '<?php echo url_for("activity_fields_list"); ?>',
    load_field_data: '<?php echo url_for("activity_field_data"); ?>',
    accept_url : '<?php echo url_for("activity_fields_data_accept"); ?>',
    delete_url: '<?php echo url_for("activity_fields_data_delete"); ?>',
  }).start();
});
</script>
