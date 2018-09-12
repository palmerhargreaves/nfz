<div id="auth-modal" class="modal" data-position="fixed">
    <div class="white modal-header">Вход в систему</div>
    <form action="<?php echo url_for('auth/auth') ?>" method="post" id="auth-form">
        <div class="modal-input-label">Электронная почта</div>
        <div class="modal-input-wrapper email">
            <input type="text" value="" name="login" id="email">
            <div class="modal-input-ok"></div>
            <div class="message narrow"></div>
        </div>
        <div class="modal-input-label">Пароль</div>
        <div class="modal-input-wrapper password">
            <input type="password" value="" name="password" id="pass">
            <div class="modal-input-ok"></div>
            <div class="message narrow"></div>
        </div>
        <div class="remember-wrapper modal-element-wrapper">
            <div class="remember">
                <input type="checkbox" name="remember" id="remember-checkbox" checked="">
                <label for="remember-checkbox"><span>Запомнить меня</span></label>
            </div>
            <div class="recovery">
                <span id="recovery-pass-link" class="modal-trigger link"
                      data-modal="#recovery-modal">Забыли пароль?</span>
            </div>
        </div>
        <div class="modal-button-wrapper">
            <button id="login-button" class="button" type="submit">Войти</button>
        </div>

        <div id="j-alert-login" class="modal-button-wrapper" style="display: none; color: red; font-weight: bold;;">
            <div class="j-message"></div>
        </div>
    </form>
    <div class="modal-trigger register-link link" data-modal="#registration-modal">Регистрация</div>
</div>
<?php use_javascript('form/form') ?>
<?php use_javascript('form/ajax_form') ?>
<script type="text/javascript">
    $(function () {
        new AjaxForm({
            form: '#auth-form',
            default_message_field: 'login',
            onSuccess: function () {
                location.href = '<?php echo url_for('auth/redirect') ?>'
            }
        }).start();

        $.krikmodal.root = '#auth-modal';
        $($.krikmodal.root).krikmodal('show');

        $("#auth-form input").focusout(function () {
            if ($(this).val())
                $(this).addClass("not-empty");
            else
                $(this).removeClass("not-empty");
        });
    });
</script>