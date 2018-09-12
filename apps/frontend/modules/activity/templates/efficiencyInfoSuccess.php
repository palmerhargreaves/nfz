<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 23.08.2016
 * Time: 13:18
 */
include_partial('agreement_activity_model_management/menu', array('active' => 'activities_efficiency_info'));

$builder->build();
$results = $builder->getResults();

?>

<table class="dealers-table" id="efficiency-table" style="z-index:9;">
    <thead>
    <tr>
        <td class="header" style="height: 185px; ">
            <h1>Эффективность</h1>

            <form action="<?php url_for('@activity_statistic_info') ?>" method="get">
                <select name="activity">
                    <?php foreach ($results['activities'] as $act): ?>
                        <option value='<?php echo $act->getId(); ?>' <?php echo !empty($activity) && $act->getId() == $activity->getId() ? 'selected' : ''; ?>><?php echo sprintf('[%s] %s', $act->getId(), $act->getName()); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if (!$activity) {
                    $activities = $results['activities']->getRawValue();
                    $activity = count($results['activities']) ? array_shift($activities) : n;
                }
                ?>

                <input placeholder="фильтр по дилерам" class="filter" type="text" name="dealer"
                       value="<?php echo isset($dealer) ? $dealer : ''; ?>"/>
                <input type='hidden' name='year' value='<?php echo $year; ?>'>
            </form>

            <button id="bt-export-efficiency-data" class="btn btn-mini" style="margin-top: 10px; float: left;" data-activity-id="<?php echo !empty($activity) ? $activity->getId() : 0; ?>">Экспорт</button>
            <img id="export-efficiency-progress" src="/images/loader.gif" style="display: none; margin-left: 10px; margin-top: 19px; float: left;" />
        </td>

        <?php
        foreach ($results['formulas'] as $formula): ?>
            <td class="header" title="<?php echo $formula->getName() ?>"
                style="height: 185px; width: 99px; vertical-align: middle;">
                <div style="width: auto; height: auto; padding: 10px; text-align: center;">
                    <span><?php echo $formula->getName() ?></span></div>
            </td>
        <?php endforeach; ?>
        <td class="header" style="height: 185px; width: 99px; vertical-align: middle;">
            <div style="width: auto; height: auto; padding: 10px; text-align: center;">Эффективность</div>
        </td>
    </tr>
    </thead>

    <tbody>
    <?php
    $n = 1;
    foreach($results['results'] as $key => $result_data):
        $dealer = DealerTable::getInstance()->find($key);
        ?>
        <tr class="dealer <?php echo $n++ % 2 == 0 ? ' odd' : ''; ?>" data-filter="<?php echo $dealer->getName(); ?>">
            <td class="header" style="">
                <div>
                    <span class="num">[<?php echo $dealer->getShortNumber() ?>]</span>
                    <a href="/activity/module/agreement/dealers/<?php echo $dealer->getId() ?>"><?php echo $dealer->getName() ?></a>
                </div>
            </td>

            <?php $efficiency = false; $efficiency_ind = 0; ?>
            <?php foreach ($results['formulas'] as $formula):
                if ($formula->isEfficiencyFormula()) {
                    $efficiency = $result_data[$formula->getId()] > 0 ? true : false;
                }
                ?>
                <td><?php echo Utils::numberFormat($result_data[$formula->getId()]); ?></td>
            <?php endforeach; ?>

            <td><img src="/images/efficiency/<?php echo $efficiency ? 'hand_up.png' : 'hand_down.png'; ?>" title="Эффективность" /></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript">
    $(function () {
        new Filter({
            field: 'table.dealers-table.clone input.filter',
            filtering_blocks: '#efficiency-table tr.dealer'
        }).start();

        new TableHighlighter({
            table_selector: '#efficiency-table',
            rows_header_selector: 'tbody tr.dealer td.header',
            columns_header_selector: 'thead td.activity'
        }).start();

        $(document).on('change', 'table.dealers-table.clone select', function () {
            this.form.submit();
        });

        new TableHeaderFixer({
            selector: '#efficiency-table'
        }).start();

        $(document).on('click', '#bt-export-efficiency-data', function() {
            var $from = $(this);

            if ($(this).data('activity-id') == 0) {
                alert('Выберите активность для экспорта.');
                return;
            }

            $from.next().show();
            $from.hide();

            $.post('<?php echo url_for('@activity_efficiency_export_data'); ?>', { activity: $(this).data('activity-id') }, function(result) {
                if (result.success) {
                    window.location.href = result.file_url;
                }

                $from.next().hide();
                $from.show();
            } );
        });
    });
</script>
