<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 08.11.16
 * Time: 12:27
 */
$month = array(
    1 => 'Январь',
    2 => 'Февраль',
    3 => 'Март',
    4 => 'Апрель',
    5 => 'Май',
    6 => 'Июнь',
    7 => 'Июль',
    8 => 'Август',
    9 => 'Сентябрь',
    10 => 'Октябрь',
    11 => 'Ноябрь',
    12 => 'Декабрь'
);
?>
<div class="container-fluid">
    <div class="row">
        <div>
            <form action="/backend.php/mailing_list" method="get">
                <div class="controls controls-row">
                    <select name="dealer_id" id="dealer-id">
                        <option value="">Выберите дилера</option>
                        <?php foreach ($dealers as $dealer): ?>
                            <option
                                value="<?= $dealer->getNumber(); ?>" <?= $dealer_id == $dealer->getNumber() ? ' selected' : ''; ?>><?= $dealer->getNumber(); ?>
                                - <?= $dealer->getName(); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="/backend.php/mailing_list" class="btn btn-default pull-right">Сбросить поиск</a>
                    <a id="xls-report" href="/backend.php/mailing_list/stat" class="btn btn-success pull-right" style="margin-right: 10px;">Выгрузить данные (XLS)</a>
                    <select name="month" id="month" class="pull-right" style="margin-right: 10px;">
                        <?php for($i = 1; $i <= 12; ++$i): ?>
                            <?php sfProjectConfiguration::getActive()->loadHelpers('Date'); ?>
                            <option value="<?= $i; ?>" <?= $month == $i ? ' selected' : ''; ?>><?= $month[$i]; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </form>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Ф.И.О</th>
                    <th>Пол</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Название фирмы</th>
                    <th>ОПФ</th>
                    <th>Vin</th>
                    <th>Модель автомобиля</th>
                    <th>Дата последнего посещения<br>дилерского центра</th>
                    <th>Дата загрузки файла</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($mailings as $m_item): ?>
                    <tr>
                        <td><?= $m_item->dealer_id; ?></td>
                        <td><?= $m_item->first_name . ' ' . $m_item->last_name . '<br>' . $m_item->middle_name; ?></td>
                        <td><?= $m_item->gender; ?></td>
                        <td><?= $m_item->phone; ?></td>
                        <td><?= $m_item->email; ?></td>
                        <td><?= $m_item->firm_name; ?></td>
                        <td><?= $m_item->opf; ?></td>
                        <td><?= $m_item->vin; ?></td>
                        <td><?= $m_item->model; ?></td>
                        <td><?= $m_item->last_visit_date; ?></td>
                        <td><?= $m_item->added_date; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#dealer-id').on('change', function () {
            $(this).closest('form').submit();
        });

        $('#xls-report').on('click', function(e) {
            e.preventDefault();
            var href = $(e.target).attr('href');
            var month = $('#month').val();
            if(month !== '') {
                href = href + '?month=' + month;
            }
            window.location = href;
        });
    })
</script>
