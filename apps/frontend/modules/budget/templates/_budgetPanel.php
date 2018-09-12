<?php
$roman = array(1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV');
$summFoQ4 = 0;

?>

<?php if ($plan && $plan->count() > 0): ?>
    <div class="budget-wrapper">

		<div class="section-header-tbl">
			<div class="d-cell"><h1><?php echo $sf_data->getRaw('header');?></h1></div>
			<div class="d-cell">
                <div id="chBudYears" class="modal-select-wrapper select input krik-select float-right">
                    <span class="select-value"><?php echo "Бюджет на " . $year . " г."; ?></span>

                    <div class="ico"></div>
                    <input type="hidden" name="year" value="<?php echo $year ?>">

                    <div class="modal-input-error-icon error-icon"></div>
                    <div class="error message"></div>
                    <div class="modal-select-dropdown">
                        <?php

                        foreach ($budYears as $year):
                            $url = !empty($fromDealer) ? url_for("@agreement_module_dealer?id=" . $dealer->getId() . "&year=" . $year) : url_for("@homepage?year=" . $year);
                            ?>
                            <div style='height:auto; padding: 7px;' class="modal-select-dropdown-item select-item"
                                 data-url="<?php echo $url ?>"><?php echo "Бюджет на " . $year . " г."; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
			</div>
		</div>

        <div class="budget">
            <div class="quarter-tabs tabs">
                <?php foreach ($plan as $n => $budget): ?>
                    <div class="tab<?php if ($n == $current_quarter) echo ' active' ?>"
                         data-pane="budget-pane<?php echo $n ?>">
                        <div class="tab-header">
                            <div class="required-activities">
                                <?php
                                $icons = $accept_stat[$n];
                                $totalAct = count($icons);

                                for ($i = 0; $i < sfConfig::get('app_activities_quarters_count'); $i++): ?>
                                    <?php if ($i < $totalAct): ?>
                                        <a href='<?php echo url_for('/activity/' . $icons[$i] . '/module/agreement/models'); ?>'><img
                                                src="/images/ok-icon-active.png" alt="Перейти в активность"
                                                title="Перейти в активность <?php echo sprintf('№ %s', $icons[$i]); ?>"/></a>
                                    <?php else: ?>
                                        <img src="/images/ok-icon.png" alt=""/>
                                    <?php endif; ?>
                                <?php endfor; ?>

                            </div>
                            <span><?php echo $roman[$budget->getQuarter()] ?></span> квартал
                        </div>
                        <div class="tab-body">
                            План: <span><?php echo number_format($budget->getPlan(), 0, '.', ' ') ?></span> руб.<br>
                            Факт: <span><?php echo number_format($real[$n], 0, '.', ' ') ?></span> руб.

                        </div>
                        <div class="tab-shadow"></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php for ($n = 1; $n <= 4; $n++): ?>
                <div class="quarter-pane" id="budget-pane<?php echo $n ?>" style="top: 120px;">
                    <div class="timeline-wrapper">
                        <div class="clock"></div>
                        <div class="line">
                            <div class="wrapper">
                                <div class="caret"
                                     data-percent="<?php echo $quarter_days[$n]['day'] / $quarter_days[$n]['length'] * 100; ?>"
                                     style="left: <?php echo $quarter_days[$n]['day'] / $quarter_days[$n]['length'] * 100 - 0.8 ?>%;"></div>
                            </div>
                        </div>
                        <div class="labels">
                            <?php for ($m = 0; $m < 3; $m++): ?>
                                <?php if ($m < 2): ?>
                                    <div class="label"><?php echo $months[$n][$m] ?></div>
                                <?php else: ?>
                                    <div class="label">
                                        <!--<span><?php echo $quarter_ends[$n] ?></span>--> <?php echo $months[$n][$m] ?></div>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php
                    $done = $plan[$n]->getPlan() == 0 ? 0 : $real[$n] / $plan[$n]->getPlan() * 100;

                    if ($done > 100)
                        $done = 100;
                    ?>
                    <div class="progressbar-wrapper">
                        <div class="sum"></div>
                        <div class="progressbar">
                            <div class="white"></div>
                            <div class="blue" data-percent="<?php echo $done ?>"><img src="/images/blue-end.png" alt="">
                            </div>
                        </div>

                        <div class="done">Выполнено: <span><?php echo number_format($real[$n], 0, '.', ' ') ?></span>
                            руб.
                        </div>

                        <?php
                        if ($real[$n] > $plan[$n]->getPlan()) {
                            ?>
                            <div class="overdraft">Перевыполнение:
                                <span><?php echo number_format($real[$n] - $plan[$n]->getPlan(), 0, '.', ' ') ?></span>
                                руб.
                            </div>
                        <?php } else if ($real[$n] != $plan[$n]->getPlan() && $n < $current_quarter) { ?>
                            <div class="overdraft">Недовыполнение:
                                <span><?php echo number_format($plan[$n]->getPlan() - $real[$n], 0, '.', ' ') ?></span>
                                руб.
                            </div>
                        <?php } else if ($real[$n] != $plan[$n]->getPlan() && $n >= $current_quarter) { ?>
                            <div class="overdraft">Осталось выполнить:
                                <span><?php echo number_format($plan[$n]->getPlan() - $real[$n], 0, '.', ' ') ?></span>
                                руб.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="year-summary">
            <h1>&nbsp;</h1>

            <div class="diagram-wrapper">
                <div class="plan">План: <span><?php echo number_format($year_plan, 0, '.', ' '); ?></span> руб.</div>
                <div id="flash-diagram"></div>

                <div class="fact">Факт: <span><?php echo number_format($year_real, 0, '.', ' '); ?></span> руб.</div>
            </div>
        </div>

        <script type="text/javascript">
            var flashvars = {
                fact: "<?php echo $year_real ?>",   //TBD: Фактический бюджет за год
                target: "<?php echo $year_plan ?>" //TBD: Запланированный бюджет на год
            };

            var params = {
                bgcolor: "efefef",
                quality: "best",
                wmode: "opaque"
            };

            swfobject.embedSWF("/flash/circle.swf", "flash-diagram", "100%", "100%", "10.0.0", null, flashvars, params);

            $(function () {
                $(document).on("click", "#chBudYears .select-item", function () {
                    location.href = $(this).data('url');
                });
            });
        </script>

        <div class="clear"></div>
    </div>
<?php endif; ?>

