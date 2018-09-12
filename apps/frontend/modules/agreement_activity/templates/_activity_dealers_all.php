<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 29.03.2016
 * Time: 6:05
 */


foreach ($extendedStats as $q => $data):
    foreach ($data as $year => $dealers):
        $total_dealers = count($dealers);
        $in_work = 0;
        $completed = 0;
        $not_work = 0;
        $total_models = 0;

        $models_completed = 0;
        $models_in_work = 0;
        ?>
        <div class="d-legend odd"><span><?php echo $year; ?></span></div>
        <table class="tbl-toggled" id="table-all">
            <tbody>
            <?php foreach ($dealers as $id => $dealer): ?>
                <?php if ($dealer['all'] > 0): ?>
                    <tr class="tbl-tr-toggle js-tbl-tr-toggle"
                        data-toggle="tr-<?= substr(strval($dealer['dealer']->getNumber()), -3) ?>">
                        <td class="tbl-toggled-status"><i>
                                <?php if ($dealer['done'] && $dealer['all'] > 0): ?>
                                    <img src="/images/ok-icon-active.png" alt="Выполнено"/>
                                <?php else: ?>
                                    <?php if ($dealer['accepted_models'] > 0): ?>
                                        <img src="/images/ok-icon-inactive.png" alt="В работе"/>
                                    <?php else: ?>
                                        <img src="/images/ico_stop.png" alt="Не приступал"/>
                                    <?php endif; ?>
                                <?php endif; ?>
                        </td>
                        <td class="tbl-toggled-title"><?php echo $dealer['dealer']->getName(), ' (', substr(strval($dealer['dealer']->getNumber()), -3), ')' ?></td>
                        <td class="tbl-toggled-summary">
                            <i></i><em><?php printf('Согласовано %d %s на сумму %s руб.', $dealer['accepted'], RusUtils::pluralModelsEnding($dealer['accepted']), number_format($dealer['sum'], 0, '.', ' ')) ?></em>
                        </td>
                    </tr>

                    <tr class="tbl-tr-toggled"
                        data-toggle="tr-<?= substr(strval($dealer['dealer']->getNumber()), -3) ?>">
                        <td colspan="3">
                            <table id="accommodation">
                                <tbody>
                                <?php foreach ($dealer['models'] as $n => $model):
                                    $total_models++;
                                    if ($model->getStatus() == "accepted" && $model->getReport() && $model->getReport()->getStatus() == "accepted") {
                                        $models_completed++;
                                    } else {
                                        $models_in_work++;
                                    }

                                    $discussion = $model->getDiscussion();
                                    $new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0;
                                    ?>
                                    <tr class="sorted-row model-row<?php echo($year ? '-ex' : '') ?> <?php if ($n % 2 == 0) echo ' even' ?>"
                                        data-model="<?php echo $model->getId() ?>"
                                        data-discussion="<?php echo $model->getDiscussionId() ?>"
                                        data-new-messages="<?php echo $new_messages_count ?>">
                                        <td class="tbl-toggled-status"></td>
                                        <td class="tbl-toggled-num" data-sort-value="<?php echo $model->getId() ?>">
                                            № <?php echo $model->getId() ?>
                                            <br/><?php echo D::toLongRus($model->created_at) ?>
                                        </td>
                                        <td class="tbl-toggled-name"
                                            data-sort-value="<?php echo $model->getName() ?>"><?php echo $model->getName() ?></td>
                                        <td class="tbl-toggled-ico <?php echo $model->getModelType()->getIdentifier() ?>">
                                            <div class="address"><?php echo $model->getValueByType('place') ?></div>
                                        </td>
                                        <td class="tbl-toggled-date"><?php echo $model->getValueByType('period') ?></td>
                                        <td class="tbl-toggled-price" data-sort-value="<?php echo $model->getCost() ?>">
                                            <?php echo number_format($model->getCost(), 0, '.', ' ') ?> руб.
                                            <div><?php $model->getSpecialistActionText() ?></div>
                                        </td>
                                        <td class="tbl-toggled-opts">
                                            <div>
                                                <?php $waiting_specialists = $model->countWaitingSpecialists(); ?>
                                                <i class="tbl-toggled-opt-status tbl-toggled-opt-<?php echo $model->getCssStatus() ?>"><?php echo $waiting_specialists ? 'x' . $waiting_specialists : '' ?></i>
                                                <?php $waiting_specialists = $model->countReportWaitingSpecialists(); ?>
                                                <i class="tbl-toggled-opt-edit tbl-toggled-opt-<?php echo $model->getReportCssStatus() ?>"><?php echo $waiting_specialists ? 'x' . $waiting_specialists : '' ?></i>
                                                <?php if ($new_messages_count > 0): ?>
                                                    <i class="tbl-toggled-opt-comments"
                                                       data-sort-value="<?php echo $new_messages_count ?>"><?php echo $new_messages_count ?></i>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        include_partial(
            'activity_dealers_stats',
            array
            (
                'year' => $year,
                'total_dealers' => $total_dealers,
                'total_models' => $total_models,
                'in_work' => $in_work,
                'models_completed' => $models_completed,
                'completed' => $completed,
                'models_in_work' => $models_in_work,
                'not_work' => $not_work
            )
        ); ?>
    <?php endforeach; ?>
<?php endforeach; ?>
