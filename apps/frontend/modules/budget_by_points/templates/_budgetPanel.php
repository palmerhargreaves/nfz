<?php use_stylesheet('budget_by_points'); ?>

<?php
$roman = array(1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV');
$summFoQ4 = 0;
?>

<?php if ($plan->count() > 0): ?>
    <div class="budget-wrapper">

        <div class="section-header-tbl">
            <div class="d-cell"><h1><?php echo $sf_data->getRaw('header'); ?></h1></div>
            <div class="d-cell">
                <div id="chBudYears" class="modal-select-wrapper select input krik-select float-right">
                    <span class="select-value">Выберите год</span>

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

        <div class="budget-by-points">
            <div class="quarter-tabs tabs">
                <?php foreach ($plan as $n => $budget): ?>
                    <div class="tab<?php if ($n == $current_quarter) echo ' active' ?>"
                         data-pane="budget-pane<?php echo $n ?>">
                        <div class="tab-header">
                            <div class="required-activities">
                                <?php
                                $icons = $accept_stat[$n];
                                $totalAct = count($icons);

                                $quarter_status = 'warning.png';

                                if ($quarters_statistics[$n]['quarter_completed']) {
                                    $quarter_status = 'ok-icon-active.png';
                                } else if (!$quarters_statistics[$n]['quarter_completed'] && !$quarters_statistics[$n]['current_quarter']) {
                                    $quarter_status = 'ok-icon.png';
                                }
                                ?>

                                <img src="/images/<?php echo $quarter_status; ?>"/></a>
                            </div>
                            <span><?php echo $roman[$budget->getQuarter()] ?></span> квартал
                        </div>

                        <?php $control_point_index = 1; ?>
                        <div class="tab-body">
                            <div class="tab-select__value">
                                <span class="tab-select__title">
                                    Контрольные пункты
                                </span>
                                <ul>
                                    <li>
                                        <span class="tab-select__text">1. Выполнение бюджета</span>
                                        <span class="status-<?php echo $quarters_statistics[$n]['quarter_plan_completed'] ? 'ok' : 'error'; ?> "></span>
                                    </li>

                                    <?php if (count($quarters_statistics[$n]['mandatory_activities']['list'])): ?>
                                        <li>
                                            <span class="tab-select__text">2. Выполнение активностей</span>
                                            <span class="status-<?php echo $quarters_statistics[$n]['mandatory_activities']['completed'] ? 'ok' : 'error'; ?>"></span>
                                        </li>

                                        <?php foreach ($quarters_statistics[$n]['mandatory_activities']['list'] as $activity): ?>
                                            <li>
                                            <span class="tab-select__text">
                                                <a class="tooltip-line" href="javascript:;">
                                                    <span class="status-<?php echo $activity['work_status']; ?>"></span>
                                                    <div class="tooltip-line-content">
                                                        <div class="tooltip-line-text" style="border-bottom: 2px solid <?php echo $activity['work_status']; ?>">
                                                            <div class="tooltip-line-inner"><?php echo $activity['work_status_msg']; ?></div>
                                                        </div>
                                                    </div>
                                                </a>

                                                <?php if ($activity['can_redirect']): ?>
                                                    <a href="<?php echo url_for("@activity_quarter_data?activity=" . $activity['id'] . "&current_q=" . $n . "&current_year=" . $quarters_statistics[$n]['year']); ?>"
                                                       style="font-weight: normal; font-size: 12px;">
                                                    <?php echo $activity['name']; ?>
                                                </a>
                                                <?php else: ?>
                                                    <?php echo $activity['name']; ?>

                                                <?php endif; ?>
                                            </span>
                                                <span class="tab-select__text">
                                                <b>
                                                    <span><?php echo $activity['id']; ?></span>
                                                </b>
                                            </span>
                                            </li>
                                        <?php endforeach; ?>

                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="wrap-data">
                                План: <span><?php echo number_format($budget->getPlan(), 0, '.', ' ') ?></span> руб.<br>
                                Факт: <span><?php echo number_format($real[$n], 0, '.', ' ') ?></span> руб.
                            </div>

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
                            <div class="line-progress"
                                 style="width: <?php echo $quarter_days[$n]['day'] / $quarter_days[$n]['length'] * 100; ?>%;"></div>
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
                    $done = $done > 100 ? 100 : $done;

                    ?>
                    <div class="progressbar-wrapper">
                        <div class="sum"></div>
                        <div class="line">
                            <div class="line-progress__blue" style="width: <?php echo $done; ?>%;"></div>
                        </div>
                        <div class="wrap-result">
                            <div class="done">Выполнено:
                                <span><?php echo number_format($real[$n], 0, '.', ' ') ?></span>
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
                </div>
            <?php endfor; ?>
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

