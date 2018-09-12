<div class="modal hide fade dialog-users-limits-modal" id="dialog-users-limits-modal" style="width: 800px; left: 45%; top: 30%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Добавить ограничения</h4>
    </div>
    <div class="modal-body" style="max-height: 650px; ">
        <div class="modal-content-container" style="width: 100%; float:left;"></div>
    </div>
    <div class="modal-footer">
        <a href="#" id="btApplyDialogLimits" class="btn pull-left">Применить</a>
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
    </div>
</div>


<script>
    $(function() {
        $(document).on('click', '.on-add-limits-to-dialog',function() {
            $.post("<?php echo url_for('@dialogs-users-limits-data'); ?>",
                {
                    id: $(this).data('dialog-id')
                },
                function (result) {
                    $('.modal-content-container').empty().html(result);
                    $("#dialog-users-limits-modal").modal('show');
                }
            )
        });

        $(document).on('keyup', '#txtFitlerSurnameName', function() {
            var $el = $(this);

            if($.trim($el.val()).length > 3) {
                options = $('#sbDialogLimitUsers option[data-surname*=' + $el.val() + ']');

                $.each(options, function(ind, el) {
                    $(el).attr('selected', true);
                });
            }
        });

        $(document).on('click', '.on-click-unselect-items', function() {
            $('#sbDialogLimitUsers option:selected').each(function() {
                this.selected = false;
            });
        });

        $(document).on('click', '#btApplyDialogLimits', function() {
            var usersPosts = [],
                    users = [];

            if($('#sbDialogLimitUserPost option:selected').length == 0 && $('#sbDialogLimitUsers option:selected').length == 0) {
                alert('Для продолжения выберите должность или пользователя.');
                return;
            }

            $('#sbDialogLimitUserPost option:selected').each(function() {
                usersPosts.push(this.value);
            });

            $('#sbDialogLimitUsers option:selected').each(function() {
                users.push(this.value);
            });

            $.post('<?php echo url_for('@dialogs-users-add-limits'); ?>',
                {
                    dialogId: $('#sbDialogLimitUserPost').data('dialog-id'),
                    usersPosts: usersPosts,
                    users: users
                },
                function(result) {
                    result = JSON.parse(result);
                    if(result.success) {
                        window.location.reload();
                    } else {
                        alert('Ошибка при добалении привязки.');
                    }
            });
        });
    });
</script>