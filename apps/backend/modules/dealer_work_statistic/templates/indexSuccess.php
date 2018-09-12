<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Выполнение бюджета</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <div
                    class="alert alert-<?php echo count($stats) > 0 ? "success" : "error"; ?> container-success"
                    style="">
                    <?php if (count($stats) > 0): ?>
                        Всего записей найдено: <?php echo count($stats); ?>
                    <?php else: ?>
                        Ничего не найдено
                    <?php endif; ?>
                </div>

                <form id='frmFilterData' action='<?php echo url_for('dealer_work_statistic'); ?>' method='post'
                      style="padding-bottom: 10px;">
                    <ul class="nav nav-list">
                        <li class="nav-header">Фильтр по дилерам</li>
                        <li>
                            Дилер:<br/>
                            <select name='sb_dealer'>
                                <option value='0'>Выберите дилера ...</option>
                                <?php
                                foreach ($dealers as $dealer) {
                                    echo '<option value="' . $dealer->getId() . '"' . ($filterDealer == $dealer->getId() ? 'selected' : '') . '>' . sprintf('[%s] %s', $dealer->getShortNumber(), $dealer->getName()) . '</option>';
                                }
                                ?>
                            </select>
                        </li>
                        <li>
                            Дата с:<br/>
                            <input type='text' name='start_date' class='date' value='<?php echo $startDateFilter; ?>'>
                        </li>
                        <li>
                            Дата до:<br/>
                            <input type='text' name='end_date' class='date' value='<?php echo $endDateFilter; ?>'>
                        </li>
                        <li>
                            Год:<br/>
                            <select id='sbByYear' name='sbByYear'>
                                <option value="-1">Выберите год ...</option>
                                <?php for ($year = date('Y') - 2, $maxYear = (date('Y')); $year <= $maxYear; $year++) { ?>
                                    <option
                                        value="<?php echo $year; ?>" <?php echo $year == $byYear ? "selected" : ""; ?>><?php echo sprintf('Год - %s', $year); ?></option>
                                <?php } ?>
                            </select>
                        </li>
                        <li>
                            <input type='button' class='btn' style='float: left; margin-right: 10px;' value='Очистить'
                                   data-url='<?php echo url_for('dealer_work_statistic_clear'); ?>'/>
                            <input type='submit' class='btn' style='float: left; margin-right: 10px;' value='Фильтр'/>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>

    <div id="dealer-activities-list" class="row-fluid">
        <?php if (isset($stats)) {

            if ($filterDealer != 0 && $byYear) {
                include_partial('data_by_dealer_year', array('stats' => $stats, 'filterDealer' => $filterDealer));
            } else {
                ?>
                <table class="table table-bordered table-striped " cellspacing="0">
                    <thead>
                    <tr>
                        <th>№</th>
                        <th>Дилер</th>
                        <th>1 Квартал</th>
                        <th>2 Квартал</th>
                        <th>3 Квартал</th>
                        <th>4 Квартал</th>
                        <th>Сумма</th>
                        <!--<th >Активностей</th>
                        <th >Моделей</th>-->
                        <th>На дату</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($stats as $item) {
                        $dealer = DealerTable::getInstance()->find($item->getDealerId());
                        ?>
                        <tr>
                            <td class="span2"><?php echo $dealer->getShortNumber(); ?> </td>
                            <td class="span3"><?php echo $dealer->getName(); ?> </td>
                            <td class="span2"><span><?php echo number_format($item->getQ1(), 0, '.', ' ') ?></span> руб.
                            </td>
                            <td class="span2"><span><?php echo number_format($item->getQ2(), 0, '.', ' ') ?></span> руб.
                            </td>
                            <td class="span2"><span><?php echo number_format($item->getQ3(), 0, '.', ' ') ?></span> руб.
                            </td>
                            <td class="span2"><span><?php echo number_format($item->getQ4(), 0, '.', ' ') ?></span> руб.
                            </td>
                            <td class="span3">
                                <span><?php echo number_format($item->getQ1() + $item->getQ2() + $item->getQ3() + $item->getQ4(), 0, '.', ' ') ?></span>
                                руб.
                            </td>
                            <td class="span3"><?php echo $item->getCreatedAt(); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
        }
        ?>
    </div>
</div>

<script>
    $(function () {
        $('#frmFilterData input.date').datepicker({dateFormat: "yy-mm-dd"});

        $('input[type=button]').click(function (e) {
            e.stopPropagation();

            $('#frmFilterData').attr('action', $(this).data('url')).submit();
        });

        $('input[type=submit').click(function (e) {
            e.preventDefault();

            var sbDealer = parseInt($('select[name=sb_dealer]').val()),
                sbYear = parseInt($('select[name=sbByYear]').val());

            if (sbYear != -1 && sbDealer <= 0) {
                alert('Для продолжения выберите дилера.');
                return;
            }

            $(this).closest('form').eq(0).submit();
        });
    });
</script>