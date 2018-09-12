<div id="sent" class="modal">
        <div class="modal-header">Восстановление пароля</div>
        <div class="modal-close"></div>
        <div class="modal-sent-text">
          Новый пароль выслан на Ваш e-mail
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
