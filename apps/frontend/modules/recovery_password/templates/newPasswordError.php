<div id="sent" class="modal">
        <div class="modal-header">Восстановление пароля</div>
        <div class="modal-close"></div>
        <div class="modal-sent-text">
          <p>Не удалось восстановить пароль!</p>
          <p>Пожалуйста, попробуйте ещё раз.</p>
          <p>Если пароль не восстанавливатся со второй попытки, обратитесь к администрации ресурса.</p>
        </div>
</div>

<script type="text/javascript">
$(function() {
  $('#sent').krikmodal('show');
  $('#sent .modal-close').click(function() {
    location.href = '<?php echo url_for('@homepage') ?>';
  });
});
</script>