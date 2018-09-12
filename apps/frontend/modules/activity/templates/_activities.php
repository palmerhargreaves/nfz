<div class="activities-intro">
    <h1><?php echo $title; ?></h1>
    <?php if (!empty($description)) : ?>
        <p><?php echo $sf_data->getRaw('description') ?></p>
    <?php endif; ?>
</div>

<?php if (count($models) > 0): ?>
    <div class="list-last-orders">
        <strong class="list-title">Последние заявки</strong>

        <?php foreach ($models as $model): ?>
            <div class="list-item">
                <div><?php echo $model->getName(); ?></div>
                <div class="list-item-file">
                    <?php if ($sf_user->getRawValue()->getAuthUser()->isSuperAdmin()): ?>
                        <div><a target="_blank"
                                href="<?php echo url_for('@discussion_switch_to_dealer?dealer=' . $model->getDealerId() . '&activityId=' . $model->getActivityId() . '&modelId=' . $model->getId()); ?>">#<?php echo $model->getId(); ?></a>
                        </div>
                    <?php else: ?>
                        <div>
                            <a href="<?php echo url_for('@agreement_module_models_model?activity=' . $model->getActivityId() . '&model=' . $model->getId()); ?>">#<?php echo $model->getId(); ?></a>
                        </div>
                    <?php endif; ?>
                    <div class="small"><?php echo $model->getActivity()->getName(); ?></div>
                </div>
                <div class="small"><span class="gray">Cтатус:</span>
                    <?php
                    $status = $model->getDealerActionText();
                    if (empty($status)) {
                        $status = $model->getSpecialistActionText();
                    }

                    echo $status;
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <li class="item">
            <a href="<?php echo url_for('@agreement_module_model_activities') ?>">Просмореть все</a>
        </li>

    </div>

<?php endif; ?>

<div class="activities-list">
    <?php $year = date('Y'); ?>
    <?php foreach ($activities as $activity):
        if (!$activity->isActiveForUser($sf_user->getRawValue()->getAuthUser()) || $activity->getFinished()) {
            continue;
        }

        ?>
        <a href="<?php echo url_for('activity/index?activity=' . $activity['id']) ?>"
           class="activity<?php if ($activity['is_viewed']) echo ' closed' ?>">
            <div class="corner"></div>
            <div class="num"><?php echo $activity['id'] ?></div>
            <div class="date">
                <?php if ($activity['custom_date']): ?>
                    <?php echo nl2br($activity['custom_date']) ?>
                <?php else: ?>
                    c <?php echo D::toLongRus($activity['start_date']) ?>
                    <br>
                    по <?php echo D::toLongRus($activity['end_date']) ?>
                <?php endif; ?>
            </div>
            <div class="text">
                <div class="title"><?php echo $activity['name'] ?></div>
                <div class="desc"><?php echo $activity->getRaw('brief') ?></div>
            </div>
            <div class="activity-status-ico">
                <div class="img-wrapper<?php if (!$activity['is_viewed']) echo ' border'; ?>">
                    <?php

                    $status_icon = null;
                    $status_icon_title = '';
                    switch ($activity->getStatus($sf_user->getRawValue()->getAuthUser(), $year)) {
//                        case ActivityModuleDescriptor::STATUS_IMPORTANCE;
//                          $status_icon = 'warn-icon.png';
//                          $status_icon_title = 'выполнение данной активности влияет на получение бонуса по маркетингу сервиса';
//                          break;
                        case ActivityModuleDescriptor::STATUS_ACCEPTED:
                            $status_icon = 'ok-icon-active.png';
                            break;
                        case ActivityModuleDescriptor::STATUS_WAIT_AGREEMENT:
                            $status_icon = 'wait-icon.png';
                            break;
                        case ActivityModuleDescriptor::STATUS_WAIT_DEALER:
                            $status_icon = 'pencil-icon.png';
                            break;
                    }
                    ?>
                    <?php if ($status_icon): ?>
                        <img src="/images/<?php echo $status_icon ?>" alt="<?php echo $status_icon_title ?>"
                             title="<?php echo $status_icon_title ?>">
                    <?php endif; ?>

                    <?php if ($activity['select_activity'] == 1): ?>
                        <img src="/images/warn-icon.png" alt="<?php ?>" title="<?php ?>">
                    <?php endif; ?>
                </div>
            </div>
        </a>
        <div class="clear"></div>
    <?php endforeach; ?>
</div>
