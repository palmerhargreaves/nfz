<?php include_partial('agreement_activity_model_management/menu', array('active' => 'activities_statistic', 'year' => $year, 'url' => 'activity_statistic_info', 'budYears' => $budgetYears)) ?>
<?php
$stat = $builder->getStat();

/*$activities = $builder->getActivitiesStat();
$total = $builder->getTotalStat();*/

if (isset($stat['fields'])):
?>

<table class="dealers-table" id="status-table" style="z-index:9;">
    <thead>
    <tr>
        <td class="header" style="height: 185px;">
            <!--<a href="#" class="save">сохранить</a>-->
            <h1>Статистика по активностям</h1>

            <form action="<?php url_for('@activity_statistic_info') ?>" method="get">
                <select name="activity">
                    <?php foreach ($stat['activities'] as $act): ?>
                        <option
                            value='<?php echo $act->getId(); ?>' <?php echo !empty($activity) && $act->getId() == $activity->getId() ? 'selected' : ''; ?>><?php echo sprintf('[%s] %s', $act->getId(), $act->getName()); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if ($stat['totalQ'] > 0): ?>
                    <select name="activityQuarter">
                        <option value="-1">Выберите квартал ...</option>
                        <?php foreach ($stat['quarters'] as $qKey => $q): ?>
                            <option
                                value="<?php echo $qKey; ?>" <?php echo !empty($activityQuarter) && $qKey == $activityQuarter ? 'selected' : ''; ?>>
                                Квартал - [<?php echo $qKey; ?>]
                            </option>
                        <?php endforeach; ?>
                    </select>

                <?php endif; ?>

                <input placeholder="фильтр по дилерам" class="filter" type="text" name="dealer"
                       value="<?php echo isset($dealer) ? $dealer : ''; ?>"/>
                <input type='hidden' name='year' value='<?php echo $year; ?>'>
            </form>
        </td>

        <?php
        foreach ($stat['fields'] as $field): ?>
            <td class="header" title="<?php echo $field->getName() ?>"
                style="height: 185px; width: 99px; vertical-align: middle;">
                <div style="width: auto; height: auto; padding: 10px; text-align: center;">
                    <span><?php echo $field->getName() ?></span></div>
            </td>
        <?php endforeach; ?>
        <td class="header" style="height: 185px; width: 99px; vertical-align: middle;">
            <div style="width: auto; height: auto; padding: 10px; text-align: center;">Дата заполнения</div>
        </td>
    </tr>
    </thead>

    <tbody>
    <?php
    $n = 1;
    foreach ($stat['dealers'] as $qKey => $quarters):
        if ($qKey != 0):
            ?>
            <tr>
                <td colspan="<?php echo count($stat['fields']) + 2; ?>"
                    style="border-top: 1px solid #d6d6d6; background: linear-gradient(180deg, #e6f0f2 0%,#d6d6d6 100%);  height: 27px; line-height: 20px; font-weight: bold; cursor: pointer;">
                    <?php echo sprintf('Квартал [%s]', $qKey); ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php
        foreach ($quarters as $dKey => $dealers):
            $dealer = $dealers['dealer'];

            ?>
            <tr class="dealer <?php if ($n % 2 == 0) echo ' odd'; ?>" data-filter="<?php echo $dealer ?>">
            <td class="header">
                <div><span class="num">[<?php echo $dealer->getShortNumber() ?>]</span> <a
                        href="/activity/module/agreement/dealers/<?php echo $dealer->getId() ?>"><?php echo $dealer->getName() ?></a>
                </div>
            </td>
            <?php
            foreach ($dealers['values']['item'] as $item):

                $itemField = ActivityFieldsTable::getInstance()->createQuery()->select('type, content')->where('id = ?', $item['field_id'])->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
                ?>
                <td style="<?php echo $itemField['type'] == "date" ? "height: 70px;" : ""; ?>">
                    <?php
                    $val = $item['val'];
                    if ($itemField['content'] == "price") {
                        $val = number_format(floatval($val), 0, '.', ' ') . ' руб.';
                    }

                    echo $val;
                    ?>
                </td>
            <?php endforeach; ?>
            <td><?php echo $dealers['update_date']; ?></td>
            <?php
            $n++;
        endforeach;
        ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<script type="text/javascript">
    $(function () {
        new Filter({
            field: 'table.dealers-table.clone input.filter',
            filtering_blocks: '#status-table tr.dealer'
        }).start();

        new TableHighlighter({
            table_selector: '#status-table',
            rows_header_selector: 'tbody tr.dealer td.header',
            columns_header_selector: 'thead td.activity'
        }).start();

        $(document).on('change', 'table.dealers-table.clone select', function () {
            this.form.submit();
        });

        /*new TableHeaderFixer({
            selector: '#status-table'
        }).start();*/
    });
</script>
