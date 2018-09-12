<ul class="pages tabs" id="activity-tabs">
    <?php if ($activity->getRawValue()->getDescription() || $activity->getInfoData()->count() > 0): ?>
        <li id="information-tab" class="tab<?php if ($active == 'info') echo ' active' ?>"><span><a
                    href="<?php echo url_for('activity/index?activity=' . $activity->getId()) ?>">Информация</a></span>
        </li>
    <?php endif; ?>
    <?php
    $activity->callWithModule(function (ActivityModuleDescriptor $descriptor) use ($active) {
        foreach ($descriptor->getActivityTabs() as $identifier => $tab):
            ?>
            <li id="<?php echo $identifier ?>-tab" class="tab<?php if ($active == $identifier) echo ' active' ?>"><span><a
                        id="<?php echo $identifier ?>-link"
                        href="<?php echo url_for($tab['uri']) ?>"<?php if (strpos($tab['uri'], 'http') === 0) echo ' target="_blank"' ?>><?php echo $tab['name'] ?></a></span>
            </li>
            <?php
        endforeach;
    }, $sf_user->getAuthUser()->getRawValue());
    ?>

    <?php
    $haveStat = false;
    if ($activity->getActivityField()->count() > 0 && ($active == 'agreement' || $active == 'statistic')):
        $haveStat = true;

        $statisticQuarter = $sf_user->getCurrentQuarter();
        $activity->callWithActivityStatisticQuarters(function ($result) use ($active, $activity, $statisticQuarter) {
            $quarters = $result->quarters;

            if ($result->totalCompleteModels > 0 || $result->totalQuarters > 1):
                foreach ($quarters as $q => $model): ?>
                    <li id="statistic-tab-<?php echo $q; ?>"
                        class="tab<?php echo $active == 'statistic' && $q == $statisticQuarter ? ' active' : ''; ?> "
                        style="white-space: nowrap;">
                        <span
                            class="<?php echo $activity->checkCompleteStatsByQAndModel($activity->getId(), $result->dealerId, $q, $model) ? "complete" : "incomplete"; ?>"><a
                                href="<?php echo url_for('@activity_statistic?activity=' . $activity->getId() . "&quarter=" . $q) ?>"><?php echo sprintf("Статистика [Квартал-%s]-", $q); ?></a></span>
                    </li>
                <?php endforeach;
            else:
                ?>
                    <li id="statistic-tab" class="<?php echo $active == 'statistic' ? ' active' : ''; ?>">
                        <a href="<?php echo url_for('@activity_statistic_one?activity=' . $activity->getId()) ?>">Статистика</a>
                    </li>
                <?php
            endif;
        },
            $sf_user->getAuthUser()->getRawValue());
        ?>
        <!--<li id="statistics-tab" class="tab<?php if ($active == 'statistic') echo ' active' ?>"><span><a href="<?php //echo url_for('@activity_statistic?activity='.$activity->getId())
        ?>">Статистика</a></span></li>-->
    <?php endif; ?>

    <?php if (!$haveStat) {
        $fieldsCount = $activity->getExtendedActivityFieldsCount();

        if ($fieldsCount > 0/* && $activity->isAlwaysOpen($sf_user->getAuthUser()->getRawValue())*/) {
            ?>
            <li id="statistics-extended-tab" class="tab<?php if ($active == 'extended_statistic') echo ' active' ?>">
                <span><a href="<?php echo url_for('@activity_extended_statistic?activity=' . $activity->getId()) ?>">Статистика</a></span>
            </li>
        <?php } else if ($activity->haveModelsWithCertificates($sf_user->getAuthUser()->getRawValue()) && $fieldsCount > 0) { ?>
            <li id="statistics-extended-tab" class="tab<?php if ($active == 'extended_statistic') echo ' active' ?>">
                <span><a href="<?php echo url_for('@activity_extended_statistic?activity=' . $activity->getId()) ?>">Статистика</a></span>
            </li>
        <?php } else if ($fieldsCount > 0 && $activity->checkForCertificateEnd($sf_user->getAuthUser()->getRawValue())) { ?>
            <li id="statistics-extended-tab" class="tab<?php if ($active == 'extended_statistic') echo ' active' ?>">
                <span><a href="<?php echo url_for('@activity_extended_statistic?activity=' . $activity->getId()) ?>">Статистика</a></span>
            </li>
        <?php } ?>
    <?php } ?>

    <?php if ($sf_user->getAuthUser()->getModelsUserDealerCount($activity->getId()) > 0 && ActivityEfficiencyFormulasTable::getInstance()->createQuery()->where('activity_id = ?', $activity->getId())->count() > 0 && $sf_user->getAuthUser()->getRawValue()->isDealerUser()): ?>
        <li id="efficiency-tab" class="tab <?php if ($active == 'efficiency') echo ' active' ?>">
            <a href="<?php echo url_for('@activity_efficiency?activity=' . $activity->getId()) ?>">Эффективность</a>
        </li>
    <?php endif; ?>
</ul>

<script type="text/javascript">
    // автоматический переход на первую вкладку, если нет активной

    if ($('#activity-tabs > li.active').length == 0) {
        var href = $('#activity-tabs > li:eq(0) a').attr('href');
        if (href)
            location.href = href;
    }
</script>
