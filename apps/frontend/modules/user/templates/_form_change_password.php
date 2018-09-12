<div id="pass-change" class="modal">
        <div class="modal-header">Смена пароля</div>
        <div class="modal-close"></div>
        <div class="modal-text">Указанный пароль будет использоваться в дальнейшем для доступа на ресурс.<br>По всем вопросам, связанным с регистрацией, пишите на <a href="mailto:nfz-support@vw-servicepool.ru">nfz-support@vw-servicepool.ru</a></div>
        <div class="modal-your-email-text"><b>Ваш E-mail</b>: <i><?php echo $sf_user->getAuthUser()->getEmail() ?></i></div>
        <form action="<?php echo url_for('user/changePassword') ?>" method="post" id="change-password-form">
          <div class="modal-input-label">Старый пароль</div>
          <div class="modal-input-wrapper reg-pass">
                  <input type="password" value="" name="old_password" id="reg-pass">
                  <div class="message narrow"></div>
          </div>
          <div class="modal-input-label">Новый пароль</div>
          <div class="modal-input-wrapper reg-pass2">
                  <input type="password" value="" name="new_password" id="reg-pass2">
                  <div class="message narrow"></div>
          </div>
          <div class="modal-button-wrapper"><input id="change-button" type="submit" class="modal-button button" value="Сменить пароль"></div>
        </form>
</div>

<div id="pass-changed" class="modal">
        <div class="modal-header">Смена пароля</div>
        <div class="modal-close"></div>
        <div class="modal-sent-text">Пароль успешно изменен.</div>
</div>

<script type="text/javascript" src="/js/form/form.js"></script>
<script type="text/javascript" src="/js/form/ajax_form.js"></script>
<script type="text/javascript">
  $(function() {
    new AjaxForm({
      form: '#change-password-form',
      success_modal: '#pass-changed',
      modal_selector: '#pass-change'
    }).start();
  });
</script>

