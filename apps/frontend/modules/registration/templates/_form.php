<div id="registration-modal" class="modal" data-position="fixed">
    <div class="modal-close"></div>
    <div class="modal-header">Регистрация</div>
    <form action="<?php echo url_for('registration/index') ?>" method="post" id="registration-form">

        <div class="modal-text">Указанные e-mail и пароль будут использоваться в дальнейшем для доступа на ресурс. По
            всем вопросам, связанным с регистрацией, пишите на <a href="mailto:nfz-support@vw-servicepool.ru">nfz-support@vw-servicepool.ru</a>.
        </div>

        <div class="modal-input-label">E-mail</div>
        <div class="modal-input-wrapper">
            <input type="text" value="" name="email" id="reg-email" placeholder="ivan.ivanov@obrazec-motors.ru"
                   data-required="true" data-format-expression="^[0-9a-z._-]+@[0-9a-z._-]+$"
                   data-format-expression-flags="i" data-right-format="ivan.ivanov@obrazec-motors.ru">
            <div class="warning message"
                 data-warning-text="Внимание! Указывайте свой рабочий адрес e-mail. На этот адрес в дальнейшем будут приходить все информационные рассылки."></div>
        </div>
        <div class="modal-input-label">Пароль</div>
        <div class="modal-input-wrapper">
            <input type="password" value="" name="password" id="reg-pass" data-required="true">
            <div class="error message"></div>
        </div>
        <div class="modal-input-label">ФИО (полностью)</div>
        <div class="modal-input-wrapper reg-fio">
            <input type="text" value="" name="fio" placeholder="Иванов Иван Иванович" data-required="true">
            <div class="error message"></div>
        </div>
        <div class="modal-input-label">Тип компании</div>
        <div class="modal-select-wrapper select krik-select">
            <span class="select-value">Дилер</span>
            <div class="ico"></div>
            <input type="hidden" name="company_type" value="dealer">
            <div class="error message"></div>
            <div class="modal-select-dropdown">
                <div class="modal-select-dropdown-item select-item" data-value="dealer">Дилер</div>
                <div class="modal-select-dropdown-item select-item" data-value="importer">Импортер</div>
                <div class="modal-select-dropdown-item select-item" data-value="regional_manager">Региональный
                    менеджер
                </div>
                <div class="modal-select-dropdown-item select-item" data-value="other">Другое</div>
            </div>
        </div>
        <div class="dealer">
            <div class="modal-select-wrapper select krik-select">
                <span class="select-value">Выберите дилерское предприятие</span>
                <span class="select-filter"><input type="text"></span>
                <input type="hidden" name="dealer_id">
                <div class="ico"></div>
                <div class="error message"></div>
                <div class="modal-select-dropdown">
                    <div class="modal-select-dropdown-item select-item" data-value="">Выберите дилерское предприятие
                    </div>
                    <?php foreach ($form->getWidget('dealer_id')->getChoices() as $id => $name): ?>
                        <div class="modal-select-dropdown-item select-item"
                             data-value="<?php echo $id ?>"><?php echo $name ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="company">
            <div class="modal-input-label">Компания</div>
            <div class="modal-input-wrapper">
                <input type="text" value="" name="company_name" id="reg-job" placeholder="ООО Сервис">
                <div class="error message"></div>
            </div>
        </div>
        <!--<div class="modal-input-label">Должность</div>
        <div class="modal-input-wrapper">
                <input type="text" value="" name="post" id="reg-job" placeholder="Маркетолог" data-required="true">
                <div class="error message"></div>
        </div>-->

        <!---->
        <div class="modal-input-label">Отдел</div>
        <div class="modal-select-wrapper select krik-select">
            <span class="select-value">Выберите отдел</span>
            <div class="ico"></div>
            <input type="hidden" name="company_department" value="" data-required="true">
            <div class="error message"></div>
            <div class="modal-select-dropdown">
                <div class="modal-select-dropdown-item select-item" data-value="1">Отдел сервиса</div>
                <div class="modal-select-dropdown-item select-item" data-value="2">Отдел маркетинга</div>
                <div class="modal-select-dropdown-item select-item" data-value="3">Отдел продаж запчастей и
                    аксессуаров
                </div>
                <div class="modal-select-dropdown-item select-item" data-value="4">Генеральный директор</div>
            </div>
        </div>

        <div class="company-post-block" style="display: none;">
            <div class="modal-input-label">Должность</div>
            <div class="company-post">
                <div style='text-align: center;'>Выберите должность</div>
            </div>
        </div>

        <!---->
        <div class="modal-input-label">Телефон</div>
        <div class="modal-input-wrapper">
            <input type="text" value="" name="phone" id="reg-phone" placeholder="+7 985 123 45 67"
                   data-format-expression="^\+[0-9]{1,2}(\s[0-9]+)+$" data-right-format="+7 985 123 45 67"
                   data-required="true">
            <div class="error message"></div>
        </div>
        <div class="modal-input-label">Мобильный телефон</div>
        <div class="modal-input-wrapper">
            <input type="text" value="" name="mobile" id="reg-phone" placeholder="+7 985 123 45 67"
                   data-format-expression="^\+[0-9]{1,2}(\s[0-9]+)+$" data-right-format="+7 985 123 45 67">
            <div class="error message"></div>
        </div>
        <div class="agree-wrapper modal-checkbox-wrapper modal-element-wrapper">
            <input type="checkbox" name="agree" id="agree-checkbox" data-required="true">
            <label for="agree-checkbox"><span>Я согласен на предоставление персональных данных</span></label>
            <div class="error message"></div>
        </div>
        <div class="modal-button-wrapper"><a href="<?php echo url_for('registration/agreement') ?>" target="_blank">Пользовательское
                соглашение</a></div>
        <div class="modal-button-wrapper">
            <button id="register-button" class="button" type="submit">Отправить заявку</button>
        </div>
    </form>
</div>

<div id="success-modal" class="modal">
    <div class="modal-header">Заявка принята</div>
    <div class="modal-close"></div>
    <div class="modal-text">
        <p>Спасибо за регистрацию, Вам на почту будет направлено подтверждение после рассмотрения заявки и активации
            аккаунта.</p>
        <p>Если у Вас возникли проблемы или вопросы, связанные с регистрацией, пишите на <a
                href="mailto:<?php echo sfConfig::get('app_mail_sender'); ?>"><?php echo sfConfig::get('app_mail_sender'); ?></a>.
        </p>
    </div>
</div>

<?php use_javascript('form/form') ?>
<?php use_javascript('form/ajax_form') ?>
<?php use_javascript('registration/registration') ?>
<script type="text/javascript">
    $(function () {
        new RegistrationForm({
            form: '#registration-form',
            success_modal: '#success-modal',
            modal_selector: '#registration-modal',
            onLoadCompanyPostUrl: '<?php echo url_for('company_dep'); ?>',
            default_messages: {
                email: 'Внимание! Указывайте свой рабочий адрес E-mail. На этот адрес в дальнейшем будут приходить все информационные рассылки.'
            }
        }).start();
    });
</script>