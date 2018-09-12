<div class="modal hide fade orders-task-modal" id="orders-task-modal">
  <div class="modal-header">
    <button type="button" class="close close-tasks-orders" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Изменение порядка задач</h3>
  </div>
  <div class="modal-body">
    <h4 class="activity-name"></h4>
    <div class="tasks"></div>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn close-tasks-orders" data-dismiss="modal" aria-hidden="true">Закрыть</a>
  </div>
</div>
<script type="text/javascript">
$(function() {
  window.tasks_orders = new TasksOrders({
    modal: '#orders-task-modal',
    tasks_url: '<?php echo url_for('@activity_tasks') ?>'
  }).start();
});
</script>
