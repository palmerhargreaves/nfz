<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <div class="alert alert-error container-error" style="display: none;"></div>
                <div class="alert alert-success container-success" style="display: none;"></div>

                <ul class="nav nav-list">
                    <li class="nav-header">Список пользователей с привязкой к дилерам</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span9">
                <div id="container-user-binded-to-dealers">
                    <?php include_partial('users_list', array('usersWithDealers' => $usersWithDealers)); ?>
                </div>
                <!--/.well -->
            </div>

            <div class="well span3">
                <ul class="nav nav-list">
                    <li class="nav-header">Добавить запись</li>
                </ul>

                <form class="form-horizontal" id='frmFilter'>
                    <div class="control-group">
                        <label class="control-label" for="sbDealersAddUser"></label>

                        <div class="controls" style="margin-left: 0px;">
                            <select id="sbDealersAddUser" name="sbDealersAddUser"> <option value='-1'>Выберите дилера</option>
                                <?php foreach ($usersWithDealers as $item): ?>
                                    <option value='<?php echo $item->getDealerId(); ?>'>
                                        <?php echo sprintf('[%d] %s', $item->getDealer()->getShortNumber(), $item->getDealer()->getName()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="sbUsersToAdd"></label>

                        <div class="controls" style="margin-left: 0px;">
                            <select id="sbUsersToAdd" name="sbUsersToAdd" style="display: none;">

                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls" style="margin-left: 0px;">
                            <button id="btAddDealerUser" type="submit" class="btn">Добавить</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="well span3">
                <ul class="nav nav-list">
                    <li class="nav-header">Фильтр</li>
                </ul>

                <form class="form-horizontal" id='frmFilter'>
                    <div class="control-group">
                        <label class="control-label" for="txtFilterByEmailName"></label>

                        <div class="controls" style="margin-left: 0px;">
                            <input type="text" id="txtFilterByEmailName" name="txtFilterByEmailName"
                                   placeholder="Email / Имя / Ф.И.О.">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="sbDealers"></label>

                        <div class="controls" style="margin-left: 0px;">
                            <select id='sbDealers' name='sbDealers'>
                                <option value='-1'>Выберите дилера</option>
                                <?php foreach ($usersWithDealers as $item): ?>
                                    <option value='<?php echo $item->getId(); ?>'>
                                        <?php echo sprintf('[%d] %s', $item->getDealer()->getShortNumber(), $item->getDealer()->getName()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls" style="margin-left: 0px;">
                            <button id="btFilterData" type="submit" class="btn">Фильтр</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--/row-->
</div>

<script>
    $(function () {
        var isHidden = false;

        $(document).on('keyup', 'input[name=txtFilterByEmailName]', function (e) {
            var $el = $(e.target), searchText = $.trim($el.val());

            searchData(searchText);
        });

        $(document).on('change', '#sbDealers', function () {
            var dealerId = $(this).val();

            searchData(dealerId);
        });

        $(document).on("click", ".on-delete-dealer-user", function () {
            var $el = $(this);

            if (confirm("Удалить запись ?")) {
                $.post('<?php echo url_for('@dealer_user_delete'); ?>',
                    {
                        id: $el.data('id')
                    },
                    function (result) {
                        result = JSON.parse(result);
                        if (result.success) {
                            $('#item-index-' + $el.data('id')).remove()
                        } else {
                            alert(result.msg);
                        }
                    });
            }
        });

        $(document).on('change', '#sbDealersAddUser', function() {
            var $el = $(this);

            if($el.val() != -1) {
                $('#sbUsersToAdd').show();

                $.post('<?php echo url_for('@dealer_load_users'); ?>',
                    {
                        id: $el.val()
                    },
                    function(result) {
                        $('#sbUsersToAdd').empty().html(result);
                    });
            } else {
                $('#sbUsersToAdd').hide();
            }
        });

        $(document).on('click', '#btAddDealerUser', function(e) {
            e.preventDefault();

            var dealerId = $('#sbDealersAddUser').val(), userId = $('#sbUsersToAdd').val();
            if(dealerId == -1 || userId == -1) {
                alert('Выберите дилера или пользователя');
                return;
            }

            $.post('<?php echo url_for('@dealer_user_add'); ?>',
                {
                    dealerId: dealerId,
                    userId: userId
                },
                function() {
                    window.location.reload();
            });

        });

        $(document).on("click", ".on-change-approve-status", function () {
            changeDealerUserData($(this), '<?php echo url_for('@dealer_user_approve'); ?>');
        });

        $(document).on("click", ".on-change-manager-status", function () {
            changeDealerUserData($(this), '<?php echo url_for('@dealer_user_manager'); ?>');
        });

        var changeDealerUserData = function ($el, url) {
            $.post(url,
                {
                    id: $el.data('id')
                },
                function (result) {
                    result = JSON.parse(result);
                    if (result.success) {
                        var $ch = $el.find('span');

                        if ($ch.hasClass('label-success')) {
                            $ch.removeClass('label-success');
                            $ch.addClass('label-error');

                            $ch.empty().html('Нет');
                        } else {
                            $ch.removeClass('label-error');
                            $ch.addClass('label-success');

                            $ch.empty().html('Да');
                        }
                    } else {
                        alert(result.msg);
                    }
                });
        }

        var searchData = function (searchText) {
            if (searchText.length > 3 || searchText != -1) {
                var searchResult = $('tr[data-user-name*=' + searchText + '], tr[data-user-email*=' + searchText + '], tr[data-user-surname*=' + searchText + '], tr[data-dealer-id=' + searchText + ']');

                if (searchResult.length > 0) {
                    $('tr.user-dealer-item').hide();

                    searchResult.each(function (ind, el) {
                        $(el).show();
                    });

                    isHidden = true;
                }
            }
            else if (isHidden) {
                $('tr.user-dealer-item').show();

                isHidden = false;
            }
        }

        $('#table-dealers-users').dataTable({
            "bJQueryUI": false,
            "bAutoWidth": false,
            "bPaginate": true,
            "bLengthChange": false,
            "bInfo": false,
            "bDestroy": true,
            "iDisplayLength": 50,
            "sPaginationType": "full_numbers",
            "sDom": '<"datatable-header"flp>t<"datatable-footer"ip>',
            "oLanguage": {
                "sSearch": "<span>Фильтр:</span> _INPUT_",
                "sLengthMenu": "<span>Отоброжать по:</span> _MENU_",
                "oPaginate": {"sFirst": "Начало", "sLast": "Посл", "sNext": ">", "sPrevious": "<"}
            },
            "aoColumnDefs": [
                {"bSortable": true}
            ]
        });
    });
</script>