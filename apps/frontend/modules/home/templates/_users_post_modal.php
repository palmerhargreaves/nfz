<div id="users-post-modal" class="intro modal" style="width: 540px; left: 40%; z-index: 1100;">
    <div class="modal-header">Уважаемый дилер!</div>
    <div class="modal-text">
        <p>Для обновления контактной информации, просим Вас отметить занимаемую Вами должность.</p>
        <p>Пожалуйста, используйте для этого выпадающий список.</p>
    </div>   

    <div class='model'>
        <form id='frmUsersPost' class='modal-form'>
            <div class="modal-input-label">Отдел</div>
            <div class="modal-select-wrapper select krik-select">
              <span class="select-value">Выберите отдел</span>
              <div class="ico"></div>
              <input type="hidden" name="company_department" value="">
              <div class="modal-select-dropdown">
                <div class="modal-select-dropdown-item select-item" data-value="1">Отдел сервиса</div>
                <div class="modal-select-dropdown-item select-item" data-value="2">Отдел маркетинга</div>
                <div class="modal-select-dropdown-item select-item" data-value="3">Отдел продаж запчастей и аксессуаров</div>
                <div class="modal-select-dropdown-item select-item" data-value="4">Генеральный директор</div>
              </div>
            </div>

            <div class="company-post-block" style="display: none;">
              <div class="modal-input-label">Должность</div>
              <div class="company-post"><div style='text-align: center;'>Выберите должность</div></div>
            </div>
            
            <div class="modal-button-wrapper" style="margin-top: 50px;">
                <button id="accept-user-post-button" class="button" type="submit" style='display: none;'>ОК</button>
            </div>
        </form>
    </div>
</div>


<script>
    $(function() {
        new UsersPostForm({
            onAcceptUserPostUrl : '<?php echo url_for('user_post_accept'); ?>',
            onLoadCompanyPostUrl : '<?php echo url_for('company_dep'); ?>',
        }).start();
    });
</script>

