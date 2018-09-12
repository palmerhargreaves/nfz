<div class="modal hide fade accept-task-modal" id="accept-task-modal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Выполнение задач</h3>
  </div>
  <div class="modal-body">
    <h4 class="activity-name"></h4>
    <form>
<?php include_component('dealer', 'selectDealers') ?>
    </form>
    <div class="tasks"></div>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
  </div>
</div>
<script type="text/javascript">
$(function() {
  window.tasks_acceptor = new TasksAcceptor({
    modal: '#accept-task-modal',
    tasks_url: '<?php echo url_for('@dealer_tasks') ?>'
  }).start();
  
});
</script>
