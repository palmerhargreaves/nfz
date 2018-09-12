<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Статистика по количеству прокомментированных макетов</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <?php if (isset($result_commented)): ?>
                    <div class="alert alert-info">
                        <strong>Прокомментировано макетов:</strong> <?php echo $result_commented ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($result_commented_by_specialist)): ?>
                <div class="alert alert-info">
                    <strong>Прокомментировано макетов специалистом:</strong> <?php echo $result_commented_by_specialist ?>
                </div>
                <?php endif; ?>

                <?php if (isset($result2)): ?>
                    <div class="alert alert-info">
                        <strong>Количество отправленных заявок:</strong> <?php echo $result2['total'] ?>
                    </div>

                    <div class="alert alert-info">
                        <strong>Количество согласованных отчетов:</strong> <?php echo $result2['withReport'] ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo url_for('comment_stat/show') ?>">
                    <ul class="nav nav-list">
                        <li class="nav-header">Фильтр</li>
                        <li>
                            Период:<br/>
                            <input type="text" id="start_date" name="start_date" placeholder="от"
                                   value="<?php echo isset($start_date) ? $start_date : '' ?>" class="input-small date">
                            -
                            <input type="text" id="end_date" name="end_date" placeholder="до"
                                   value="<?php echo isset($end_date) ? $end_date : '' ?>" class="input-small date">
                        </li>
                        <!--<li>
                            <label class="checkbox">
                                <input type="checkbox" name="make_changes"
                                       value='1' <?php echo $make_changes ? "checked" : ""; ?> > В макет не вносились
                                изменения
                            </label>
                        </li>-->
                        <li>
                            <input type="submit" id="btDoFilterData" class="btn" style="margin-top: 15px;"
                                   value="Фильтр"/>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">
                        список выгруженных данных за период
                        <button id="btCompareCommentsModelPeriodsStats" class="btn btn-mini" style="float: right;">
                            Сравнить
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div id="models-list-with-commented-stats" class="row-fluid">
        <div class="well">
            <?php include_partial('models_list', array('items' => $items)); ?>
        </div>
    </div>
</div>

<div class="modal hide fade model-models-comments-stats-modal" id="model-models-comments-stats-modal"
     style="width: 850px; left: 45%; top: 30%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Сравнение данных</h4>
    </div>
    <div class="modal-body" style="max-height: 650px; ">
        <div class="modal-content-container" style="width: 100%; float:left;"></div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
    </div>
</div>

<script type="text/javascript">
    $('input.date').datepicker({dateFormat: "dd.mm.y"});

    var table = $('.table-models-reports-stats').dataTable({
        "bJQueryUI": false,
        "bAutoWidth": false,
        "bPaginate": true,
        "bLengthChange": false,
        "bInfo": false,
        "bDestroy": true,
        "iDisplayLength": 25,
        "sPaginationType": "full_numbers",
        //"sDom": '<"datatable-header"flp>t<"datatable-footer"ip>',
        "oLanguage": {
            "sSearch": "<span>Фильтр:</span> _INPUT_",
            "sLengthMenu": "<span>Отоброжать по:</span> _MENU_",
            "oPaginate": {"sFirst": "Начало", "sLast": "Посл", "sNext": ">", "sPrevious": "<"}
        },
        "aoColumnDefs": [
            {"bSortable": false, "aTargets": [[1]]}
        ]
    });

    $(document).on('click', '.action-delete-report-stats', function (e) {
        if (confirm('Удалить статистику ?')) {
            var id = $(this).data('id');
            $.post('<?php echo url_for('@delete-models-comments-stats'); ?>',
                {
                    id: id
                },
                function () {
                    $('.tr-report-stats-' + id).remove();
                });
        }
    });

    $(document).on('click', '#btCompareCommentsModelPeriodsStats', function () {
        var items = getCheckedItems(), $bt = $(this);

        if (items.length < 2) {
            alert('Для продолжения необходимо выбрать несколько статистик');
            return;
        }

        $bt.hide();
        $.post("<?php echo url_for('@models-comments-stats-compare'); ?>",
            {
                items: items
            },
            function (result) {
                $('.modal-content-container').empty().html(result);
                $("#model-models-comments-stats-modal").modal('show');

                $bt.show();
            }
        );
    });

    $(document).on('click', '.not-in-compare-list', function () {
        var status = $(this).data('status'),
            $el = $(".not-in-compare-list-" + status);

        if ($el.hasClass('showed')) {
            $el.fadeOut().removeClass('showed');
        } else {
            $el.fadeIn().addClass('showed');
        }

    });

    var getCheckedItems = function () {
        var items = [];

        $(".ch-report-stats-item").each(function (ind, el) {
            if ($(el).is(":checked")) {
                items.push($(el).data('id'));
            }
        });

        return items;
    }

</script>