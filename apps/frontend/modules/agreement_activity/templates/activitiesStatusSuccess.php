<?php include_partial('agreement_activity_model_management/menu', array('active' => 'activities_status', 'year' => $year, 'url' => 'agreement_module_activities_status')) ?>

<table class="dealers-table" id="status-table" style="z-index:9;">
    <thead>
    <tr>
        <td class="header" style="height: 185px;">
            <!--<a href="#" class="save">сохранить</a>-->
            <h1 style="margin-top: 16px;">Статус выполнения активностей</h1>

            <form action="<?php url_for('@agreement_module_activities_status') ?>" method="get">
                <select name="year" style="width: 170px;">
                    <?php foreach ($budgetYears as $item): ?>
                        <option
                            value="<?php echo $item; ?>" <?php echo $item == $year ? "selected" : ""; ?>><?php echo "Бюджет на " . $item . " г."; ?></option>
                        <?php
                    endforeach;
                    ?>
                </select>

                <select name="quarter" style="width: 170px;">
                    <option value="">за весь год</option>
                    <option value="1"<?php echo $quarter == 1 ? ' selected' : '' ?>>за I квартал</option>
                    <option value="2"<?php echo $quarter == 2 ? ' selected' : '' ?>>за II квартал</option>
                    <option value="3"<?php echo $quarter == 3 ? ' selected' : '' ?>>за III квартал</option>
                    <option value="4"<?php echo $quarter == 4 ? ' selected' : '' ?>>за IV квартал</option>
                </select>

                <input placeholder="фильтр по дилерам" class="filter" type="text" name="dealer"
                       value="<?php echo $dealer ?>"/>
            </form>
        </td>
        <td class="activity" title="Процент выполнения бюджета">
            <div>
                <span>Процент выполнения бюджета</span>
            </div>
        </td>
        <td class="activity" title="Завершённых активностей с начала года">
            <div>
                <span>Завершено с начала года (активности)</span>
            </div>
        </td>
        <td class="activity" title="Завершённых заявок с начала года">
            <div>
                <span>Завершено с начала года (заявок)</span>
            </div>
        </td>
        <?php if ($quarter): ?>
            <td class="activity" title="Завершённых активностей за квартал">
                <div>
                    <span>Завершено за квартал (активностей)</span>
                </div>
            </td>

            <td class="activity" title="Завершённых заявок за квартал">
                <div>
                    <span>Завершено за квартал (заявок)</span>
                </div>
            </td>
        <?php endif; ?>
        <?php
        $activeActivities = array();
        foreach ($activities as $activity_row):
            $activeActivities[$activity_row['activityId']] = $activity_row['activityId'];
            ?>
            <td class="activity" title="<?php echo $activity_row['activityName']; ?>">
                <div><span style="overflow: initial; left: -57px;"><?php echo $activity_row['activityName'] ?></span>
                </div>
            </td>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td title="средний % выполнения бюджета"><?php echo isset($total['average_percent']) ? round($total['average_percent']) : 0; ?>%</td>
        <td></td>
        <td></td>
        <?php if ($quarter): ?>
            <td><?php ?></td>
            <td><?php ?></td>
        <?php endif; ?>
        <?php foreach ($workStats as $key => $workStat): ?>
            <td title="кол-во дилеров, завершивших активность"><?php echo $workStat['completed']; ?></td>
        <?php endforeach; ?>
    </tr>

    <tr class="dealer odd">
        <td>Количество дилеров, в работе</td>
        <td></td>
        <td></td>
        <td></td>
        <?php if ($quarter): ?>
            <td></td>
            <td></td>
        <?php endif; ?>
        <?php foreach ($workStats as $key => $workStat): ?>
            <td title="кол-во дилеров, выполняющих активность"><?php echo $workStat['in_work']; ?></td>
        <?php endforeach; ?>
    </tr>

    <?php foreach ($managers as $manager):
        $managerData = NaturalPersonTable::getInstance()->find($manager['manager_id']);
        if (!$managerData) {
            $managerName = "Без менеджера";
        } else {
            $managerName = sprintf('%s %s', $managerData->getFirstName(), $managerData->getSurname());
        }
        ?>
        <tr class="regional-manager filter-group">
            <td class="header">
                <div><?php echo $managerName; ?></div>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <?php if ($quarter): ?>
                <td></td>
                <td></td>
            <?php endif; ?>
            <?php for ($i = 0, $l = count($activities); $i < $l; $i++): ?>
                <td></td>
            <?php endfor; ?>
        </tr>
        <?php
        $n = 1;
        foreach ($dealers[$manager['manager_id']] as $dealer):
            $dealerActivities = DealerActivitiesStatsDataTable::getDataBy($dealer['id'], $year);

            $totalDealerActivitiesCompleted = DealerActivitiesStatsDataTable::getTotalActivitiesCompleted($dealer['id']);
            ?>
            <tr class="dealer <?php if ($n++ % 2 == 0) echo ' odd'; ?>"
                data-filter="<?php echo $dealer['DealerStat']['name'] ?>">
                <td class="header">
                    <div><span class="num"><?php //echo $dealer->getShortNumber() ?></span> <a
                            href="/activity/module/agreement/dealers/<?php echo $dealer['DealerStat']['id'] ?>"><?php echo $dealer['DealerStat']['name'] ?></a>
                    </div>
                </td>
                <td><?php echo round($dealer['percent_of_budget']) ?>%</td>
                <td><?php echo round($totalDealerActivitiesCompleted/*$dealer['activities_completed']*/) ?></td>
                <td><?php echo round($dealer['models_completed']) ?></td>
                <?php if ($quarter): ?>
                    <td><?php echo round($dealer['q_activity' . $quarter]) ?></td>
                    <td><?php echo round($dealer['q' . $quarter]) ?></td>
                <?php endif; ?>

                <?php foreach ($dealerActivities as $item):
                    if(!in_array($item['activity_id'], $activeActivities)) {

                        continue;
                    } else {
                        $activityStatusInfo = DealerActivitiesStatsDataTable::getActivityStatus($item);
                    ?>
                    <td class="<?php echo $activityStatusInfo['complete'] != 0 ? "ok" : ($activityStatusInfo['in_work'] > 0 ? "wait" : ""); ?>">
                        <?php
                            if ($activityStatusInfo['in_work'] > 0 || $activityStatusInfo['complete'] > 0) {
                                //url_for('@default?module=frontend&action=ActivitiesStatus&id='.$job->getId())
                                //'/activity/module/agreement/activities/".$activity->getRawValue()->getId()."'
                                echo "<a href='/activity/module/agreement/activities/" . $item['activity_id'] . "?dealer=" . $dealer['DealerStat']['id'] . "'>&nbsp</a>";
                            }
                        ?>
                    </td>
                <?php
                    }
                 endforeach;

                ?>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
/*$managers = $builder->build();
$activities = $builder->getActivitiesStat();
$total = $builder->getTotalStat();*/
?>

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

        new TableHeaderFixer({
            selector: '#status-table'
        }).start();
    })
</script>
