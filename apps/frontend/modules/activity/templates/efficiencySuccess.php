<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 28.07.2016
 * Time: 17:39
 */

$results = array();
foreach ($efficiency_result as $key => $data) {
    $results[] = $data['value'];
}


?>
<div class="activity">
    <?php include_partial('activity/activity_head', array('activity' => $activity, 'quartersModels' => $quartersModels, 'current_q' => $current_q, 'current_year' => $current_year)); ?>

    <div class="content-wrapper">
        <?php include_partial('activity/activity_tabs', array('activity' => $activity, 'active' => 'efficiency')) ?>
        <div class="efficiency-wrap d-cb">
            <?php if ($activity->getEfficiencyDescription()): ?>
                <div class=""><p><?php echo $activity->getEfficiencyDescription(); ?></p></div>
            <?php endif; ?>

            <div class="efficiency-chart">
                <div class="efficiency-chart-i">
                    <div class="efficiency-chart-c" style="top:50%"><span>Эффективность <br/>вашей акции</span></div>
                </div>
            </div>

            <div class="efficiency-info">
                <?php foreach ($efficiency_result as $key => $data): ?>
                    <?php if ($data['formula']->isEfficiencyFormula() && $data['value'] < 0): ?>
                        <div class="efficiency-info-notice"><strong>Вам необходимо изменить тактику проведения
                                кампании.</strong><br/>Сейчас эффективность вашей кампании значительно ниже минимальной
                            нулевой
                            отметки
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <table>
                    <tbody>
                    <?php foreach ($efficiency_result as $key => $data): ?>
                        <?php if (!is_object($data['value'])): ?>
                            <tr>
                                <td>
                                    <?php echo $data['formula']->getName(); ?>
                                    <?php if ($data['formula']->getDescription()): ?>
                                        <p><?php echo $data['formula']->getDescription(); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="value efficiency-graph-value" data-value="<?php echo $data['value']; ?>"
                                    data-formula-type="<?php echo $data['formula']->getActivityEfficiencyWorkFormulas()->getType(); ?>">
                                    <span><?php echo $data['value']; ?></span> руб.
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="efficiency-info-caption">Минимальная нулевая отметка — показатель эффективности, когда
                    разница между выручкой и расходами равна нулю
                </div>
            </div>
        </div><!-- /efficiency-wrap -->
    </div>
</div>

<script>
    $(function () {
        window.efficiency_arrow_slider = new ActivityEfficiencyGraphArrow({
            arrow_slider_selector: '.efficiency-chart-c',
            allow_animate_digits: true
        }).start();
    });
</script>
