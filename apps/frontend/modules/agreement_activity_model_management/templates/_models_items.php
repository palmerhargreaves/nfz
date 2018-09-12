<?php

$k = 0;
$ind = 1;
$new_messages_count = 0;

foreach ($models as $n => $model):

    /*$isBlocked = false;
    if($sf_data->getRaw('model_status_filter') == 'blocked' || $sf_data->getRaw('wait_filter') == 'blocked') {
        $isBlocked = true;

        if(!$model->isOutOfDate()) {
          continue;
        }
    } else if($model->isOutOfDate() && empty($model_filter))
      continue;*/

    //$discussion = $model->getDiscussion();
    //$new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0

    if (getenv('REMOTE_ADDR') == '46.175.166.61'):
    ?>

        <tr class="sorted-row model model-row<?php if ($k % 2 == 0) echo ' even' ?>"
            data-model="<?php echo $model->getId() ?>"
            data-discussion="<?php echo $model->getDiscussionId() ?>"
            data-new-messages="<?php echo $new_messages_count ?>"
            data-is-blocked='<?php echo $model->getIsBlocked() && !$model->getAllowUseBlocked() ? 1 : 0; ?>'>
            <td data-sort-value="<?php echo $ind ?>"><?php echo $ind++; ?></td>
            <td data-sort-value="<?php echo $model->getId() ?>">
                <div class="num">№ <?php echo $model->getId() ?></div>
                <div class="date"><?php echo D::toLongRus($model->created_at) ?></div>
                <?php
                if ($sf_user->getAuthUser()->isSuperAdmin()):
                    if ($model->getIsBlocked() && !$model->getAllowUseBlocked()):
                        ?>
                        <input type="button" class="button small unblock-model" value="Разблокировать"
                               data-model-id="<?php echo $model->getId(); ?>"
                               style="margin-top: 5px; margin-bottom: 5px; z-index: 999;">
                    <?php endif;
                endif;
                ?>
            </td>
            <td data-sort-value="<?php echo $model->getDealer()->getName() ?>"><?php echo $model->getDealer()->getName(), ' (', $model->getDealer()->getNumber(), ')' ?></td>
            <td data-sort-value="<?php echo $model->getName() ?>">
                <div><?php echo $model->getName() ?></div>
                <div class="sort"></div>
            </td>
            <?php /*<td class="placement <?php echo $model->getModelType()->getIdentifier() ?>"><div class="address"><?php echo $model->getValueByType('place') ?></div></td> */
            ?>
            <td><?php echo $model->getValueByType('period') ?></td>
            <td data-sort-value="<?php echo $model->getCost() ?>">
                <div><?php echo number_format($model->getCost(), 0, '.', ' ') ?> руб.</div>
                <div class="sort"></div>
            </td>
            <td class="darker">
                <div><?php echo $wait_filter == 'specialist' ? $model->getSpecialistActionText() : $model->getManagerActionText() ?></div>
                <div class="sort"></div>
            </td>

            <?php if ($model->getStatus() != 'not_sent' && $model->getStatus() != 'declined'): ?>
                <?php if (($sf_user->isSpecialist() || $sf_user->isManager()) && ($model->getCssStatus() != 'ok')) { ?>
                    <?php if (!empty($n)) { ?>
                        <td class="darker"
                            style="<?php echo $model->isModelAcceptActiveToday(!$designer_filter ? false : true) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                            <div><?php echo date('H:i d-m-Y', $n); ?></div>
                            <div class="sort"></div>
                        </td>
                    <?php } else { ?>
                        <td class="darker">
                            <div></div>
                        </td>
                    <?php } ?>
                <?php } else {
                    if ($model->getReport() && $model->getReport()->getStatus() != "accepted" && $model->getReport()->getStatus() != 'declined' && $model->getReport()->getStatus() != 'not_sent'):
                        ?>
                        <td class="darker"
                            style="<?php echo $model->isModelAcceptActiveToday(!$designer_filter ? false : true) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                            <div><?php echo date('H:i d-m-Y', $n); ?></div>
                        </td>
                    <?php else: ?>
                        <td class="darker" style="">
                            <div class="sort"></div>
                        </td>
                    <?php endif;
                } ?>
            <?php else: ?>
                <td class="darker" style="">
                    <div class="sort"></div>
                </td>
            <?php endif; ?>

            <?php $waiting_specialists = $model->countWaitingSpecialists(); ?>
            <td class="darker">
                <div
                    class="<?php echo $model->getCssStatus() ?>"><?php echo $waiting_specialists ? 'x' . $waiting_specialists : '' ?></div>
            </td>
            <?php $waiting_specialists = $model->countReportWaitingSpecialists(); ?>
            <td class="darker">
                <div
                    class="<?php echo $model->getReportCssStatus() ?>"><?php echo $waiting_specialists ? 'x' . $waiting_specialists : '' ?></div>
            </td>
            <td data-sort-value="<?php echo $new_messages_count ?>" class="darker">
                <div class="message message-model-<?php echo $model->getId(); ?>" style="display: none;"></div>
            </td>
        </tr>
<?php else: ?>
    <tr class="sorted-row model model-row<?php if ($k % 2 == 0) echo ' even' ?>"
        data-model="<?php echo $model->getId() ?>"
        data-discussion="<?php echo $model->getDiscussionId() ?>"
        data-new-messages="<?php echo $new_messages_count ?>"
        data-is-blocked='<?php echo $model->getIsBlocked() && !$model->getAllowUseBlocked() ? 1 : 0; ?>'>
        <td data-sort-value="<?php echo $ind ?>"><?php echo $ind++; ?></td>
        <td data-sort-value="<?php echo $model->getId() ?>">
            <div class="num">№ <?php echo $model->getId() ?></div>
            <div class="date"><?php echo D::toLongRus($model->created_at) ?></div>
            <?php
            if ($sf_user->getAuthUser()->isSuperAdmin()):
                if ($model->getIsBlocked() && !$model->getAllowUseBlocked()):
                    ?>
                    <input type="button" class="button small unblock-model" value="Разблокировать"
                           data-model-id="<?php echo $model->getId(); ?>"
                           style="margin-top: 5px; margin-bottom: 5px; z-index: 999;">
                <?php endif;
            endif;
            ?>
        </td>
        <td data-sort-value="<?php echo $model->getDealer()->getName() ?>"><?php echo $model->getDealer()->getName(), ' (', $model->getDealer()->getNumber(), ')' ?></td>
        <td data-sort-value="<?php echo $model->getName() ?>">
            <div><?php echo $model->getName() ?></div>
            <div class="sort"></div>
        </td>
        <?php /*<td class="placement <?php echo $model->getModelType()->getIdentifier() ?>"><div class="address"><?php echo $model->getValueByType('place') ?></div></td> */
        ?>
        <td><?php echo $model->getValueByType('period') ?></td>
        <td data-sort-value="<?php echo $model->getCost() ?>">
            <div><?php echo number_format($model->getCost(), 0, '.', ' ') ?> руб.</div>
            <div class="sort"></div>
        </td>
        <td class="darker">
            <div><?php echo $wait_filter == 'specialist' ? $model->getSpecialistActionText() : $model->getManagerActionText() ?></div>
            <div class="sort"></div>
        </td>

        <?php if ($model->getStatus() != 'not_sent' && $model->getStatus() != 'declined'): ?>
            <?php if (($sf_user->isSpecialist() || $sf_user->isManager()) && ($model->getCssStatus() != 'ok')) { ?>
                <?php if (!empty($n)) { ?>
                    <td class="darker"
                        style="<?php echo $model->isModelAcceptActiveToday(!$designer_filter ? false : true) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                        <div><?php echo date('H:i d-m-Y', $n); ?></div>
                        <div class="sort"></div>
                    </td>
                <?php } else { ?>
                    <td class="darker">
                        <div></div>
                    </td>
                <?php } ?>
            <?php } else {
                if ($model->getReport() && $model->getReport()->getStatus() != "accepted" && $model->getReport()->getStatus() != 'declined' && $model->getReport()->getStatus() != 'not_sent'):
                    ?>
                    <td class="darker"
                        style="<?php echo $model->isModelAcceptActiveToday(!$designer_filter ? false : true) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                        <div><?php echo date('H:i d-m-Y', $n); ?></div>
                    </td>
                <?php else: ?>
                    <td class="darker" style="">
                        <div class="sort"></div>
                    </td>
                <?php endif;
            } ?>
        <?php else: ?>
            <td class="darker" style="">
                <div class="sort"></div>
            </td>
        <?php endif; ?>

        <?php $waiting_specialists = $model->countWaitingSpecialists(); ?>
        <td class="darker">
            <div
                class="<?php echo $model->getCssStatus() ?>"><?php echo $waiting_specialists ? 'x' . $waiting_specialists : '' ?></div>
        </td>
        <?php $waiting_specialists = $model->countReportWaitingSpecialists(); ?>
        <td class="darker">
            <div
                class="<?php echo $model->getReportCssStatus() ?>"><?php echo $waiting_specialists ? 'x' . $waiting_specialists : '' ?></div>
        </td>
        <td data-sort-value="<?php echo $new_messages_count ?>" class="darker">
            <div class="message message-model-<?php echo $model->getId(); ?>" style="display: none;"></div>
        </td>
    </tr>
<?php endif; ?>
    <?php $k++; endforeach; ?>

<script>
    $(function () {
        window.model_discussion_load = new ModelDiscussionCountLoad({
            load_url: '<?php echo url_for('@agreement_models_load_discussion_count'); ?>',
            designer_filter: <?php echo !$designer_filter ? 0 : 1; ?>
        }).start();
    });
</script>
