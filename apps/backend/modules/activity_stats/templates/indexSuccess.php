<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Статистика по активностям</li>
                    <li >
                        <input name="btExportToExcel" type="button" class="btn" value="Выгрузить в Excel"
                               style="float: right; top: -20px; position: relative;"
                               data-activity="<?php echo $activity_filter; ?>"
                               data-filter-by-quarter="<?php echo $activity_filter_quarter; ?>"
                               data-filter-by-month="<?php echo $activity_filter_month; ?>"
                               data-filter-by-year="<?php echo $activity_filter_year; ?>"
                               data-work-in-redactor="<?php echo $activity_filter_redactor; ?>"
                               data-report-complete="<?php echo $activity_report_complete; ?>"
                               data-check-quarter-by-calendar="<?php echo $check_quarter_by_calendar_filter; ?> ">
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <form action="<?php echo url_for('activity_stats/index') ?>" method="get" class="form-inline"
                      id="activity-stats-form">

                    <table cellpadding='5' cellspacing='5' style='width: 100%;'>
                        <tr>
                            <td style='width: 70px;'>Активность</td>
                            <td>
                                <select id='activity_filter' name='activity_filter' style='width: 300px;'>
                                    <option value="-1">Все активности ...</option>
                                    <?php foreach ($activities as $activity): ?>
                                        <option value="<?php echo $activity->getId() ?>" <?php echo $activity_filter == $activity->getId() ? 'selected' : ''; ?>><?php echo sprintf('[%s] %s', $activity->getId(), $activity->getName()); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Период</td>
                            <td>
                                <select id='filter_by_quater' name='filter_by_quater'>
                                    <option value="-1">Выберите квартал ...</option>
                                    <?php
                                    for ($i = 1; $i <= 4; $i++) {
                                        $sel = '';
                                        if ($activity_filter_quarter == $i) $sel = 'selected';

                                        echo "<option value={$i} {$sel}>{$i}</option>";
                                    }
                                    ?>
                                </select>
                                -
                                <select id='filter_by_month' name='filter_by_month'>
                                    <option value="-1">Выберите месяц ...</option>
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $sel = '';
                                        if ($activity_filter_month == $i) $sel = 'selected';

                                        echo "<option value='" . ($i < 10 ? '0' . $i : $i) . "' {$sel}>{$i}</option>";
                                    }
                                    ?>
                                </select>
                                -
                                <select id='filter_by_year' name='filter_by_year'>
                                    <?php
                                    foreach (Utils::getYearsList(2013) as $year) {
                                        echo "<option value={$year} ".($activity_filter_year == $year ? "selected" : "").">{$year}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style='width: 200px;'>
                                <input type='checkbox' name='check_quarter_by_calendar'
                                       id='check_quarter_by_calendar' <?php echo $check_quarter_by_calendar_filter ? "checked" : "" ?>>
                                <label for='check_quarter_by_calendar'>Учитывать начало квартала по календарю</label>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td style='width: 200px;'>
                                <input type='checkbox' name='report_complete'
                                       id='report_complete' <?php echo $activity_report_complete ? "checked" : "" ?>>
                                <label for='report_complete'>Выполненные отчеты</label>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style='width: 200px;'>
                                <input type='checkbox' name='work_in_redactor'
                                       id='work_in_redactor' <?php echo $activity_filter_redactor ? "checked" : "" ?>>
                                <label for='work_in_redactor'>Макет выполнен с помощью редактора</label>
                            </td>
                        </tr>
                    </table>

                </form>
            </div>
        </div>
    </div>

    <?php
    if ($models) {
        ?>

        <div class="row-fluid">
            <div class="span12">
                <div class="well sidebar-nav">

                    <h4>Список макетов (всего: <?php echo count($models); ?>)</h4>

                    <table class="table table-bordered table-striped " cellspacing="0">
                        <thead>
                        <tr>
                            <th>Дилер (название и номер)</th>
                            <th style="text-align: center;">Номер макета</th>
                            <th>Название макета</th>
                            <th style="text-align: center;">Тип</th>
                            <th>Размер (если есть)</th>
                            <th>Период</th>
                        </tr>
                        </thead>

                        <?php
                        foreach ($models as $model) {
                            $dealer = $model->getDealer();

                            $fields = AgreementModelFieldTable::getInstance()->createQuery()->select()->where('model_type_id = ?', $model->getModelType()->getId())->andWhere('identifier = ? or identifier = ?', array('period', 'size'))->execute();
                            ?>
                            <tr>
                                <td class="span3"><?php echo sprintf("%s (%s)", $dealer->getName(), $dealer->getNumber()); ?></td>
                                <td class="span3" style="text-align: center;"><?php echo $model->getId(); ?></td>
                                <td class="span3">
                                    <?php echo $model->getName(); ?><br/>
                                    <span style="font-size: 12px;"><?php echo $model->getUpdatedAt(); ?></span>
                                </td>
                                <td class="span3"><?php echo $model->getModelType()->getIdentifier(); ?></td>
                                <td class="span3">
                                    <?php
                                    foreach ($fields as $field) {
                                        if ($field->getIdentifier() == 'size') {
                                            $value = AgreementModelValueTable::getInstance()->createQuery()->select()->where('model_id = ? and field_id = ?', array($model->getId(), $field->getId()))->fetchOne();

                                            if ($value)
                                                echo $value->getValue();
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="span3">
                                    <?php
                                    foreach ($fields as $field) {
                                        if ($field->getIdentifier() == 'period') {
                                            $value = AgreementModelValueTable::getInstance()->createQuery()->select()->where('model_id = ? and field_id = ?', array($model->getId(), $field->getId()))->fetchOne();

                                            if ($value)
                                                echo $value->getValue();
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).on('change', '#activity_filter, #filter_by_quater, #filter_by_month, #filter_by_year', function () {
        $('#activity-stats-form').submit();
    });

    $(document).on('click', '#work_in_redactor, #report_complete, #check_quarter_by_calendar', function () {
        $('#activity-stats-form').submit();
    });

    $(document).on('click', 'input[name=btExportToExcel]', function () {
        $.post("<?php echo url_for('@activity_export_to_excel'); ?>", {
            activity_filter: $(this).data('activity'),
            filter_by_quater: $(this).data('filter-by-quarter'),
            filter_by_month: $(this).data('filter-by-month'),
            filter_by_year: $(this).data('filter-by-year'),
            work_in_redactor: $(this).data('work-in-redactor'),
            report_complete: $(this).data('report-complete'),
            check_quarter_by_calendar: $(this).data('check-quarter-by-calendar')
        }, function (result) {
            window.location.href = result.url;
        });
    });
</script>
