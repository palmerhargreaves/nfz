  <div id="recovery-modal" class="modal" data-position="fixed">
          <div class="modal-header">Восстановление пароля</div>
          <div class="modal-close"></div>
    <form action="<?php echo url_for('recovery_password/recovery') ?>" method="post" id="recovery-form">
          <div class="modal-input-label">E-mail</div>
          <div class="modal-input-wrapper reg-email">
                  <input type="text" value="" name="email" id="reg-email">
                  <div class="warning narrow message" data-warning-text="Внимание! указывайте свой рабочий адрес E-mail, который был указан при регистрации"></div>
          </div>
          <div class="modal-button-wrapper">
            <button id="recovery-button" class="button" type="submit">Восстановить</button>
          </div>
    </form>
  </div>

<div id="sent-modal" class="modal" data-position="fixed">
        <div class="modal-header">Восстановление пароля</div>
        <div class="modal-close"></div>
        <div class="modal-sent-text">Инструкция по восстановлению пароля выслана Вам на E-mail</div>
</div>

<?php use_javascript('form/form') ?>
<?php use_javascript('form/ajax_form') ?>
<script type="text/javascript">
  $(function() {
    new AjaxForm({
      form: '#recovery-form',
      success_modal: '#sent-modal',
      modal_selector: '#recovery-modal'
    }).start();
  });
</script>