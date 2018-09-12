<?php
if ($builder) {
    //include_partial('agreement_activity_model_management/modal_model', array('decline_reasons' => $decline_reasons, 'decline_report_reasons' => $decline_report_reasons, 'specialist_groups' => $specialist_groups)) ?>

    <div class="actions-wrapper">
        <div class="activities" id="agreement-models">
            <h1 style="margin-top: 10px;">Активности</h1>
            <?php $quarters = array(1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV') ?>
            <div id="materials" class="active">
                <div id="accommodation" class="active">
                    <?php

                    foreach ($builder->getStat() as $quarter => $stat): ?>
                        <h2><?php echo $quarters[$quarter] ?> Квартал</h2>
                        <?php foreach ($stat['activities'] as $activity): ?>
                            <div class="group">
                                <div class="group-header">
                                    <span class="title"><?php echo sprintF('[%s] %s', $activity['activity']->getId(), $activity['activity']->getName()) ?></span>

                                    <div class="summary"><?php echo number_format($activity['sum'], 0, '.', ' ') ?>
                                        руб.
                                    </div>
                                    <div class="group-header-toggle"></div>
                                </div>
                                <div class="group-content">
                                    <table class="models">
                                        <tbody>
                                        <?php foreach ($activity['models'] as $n => $model): ?>
                                            <?php $discussion = $model->getDiscussion() ?>
                                            <?php $new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0 ?>
                                            <tr class="sorted-row <?php if ($n % 2 == 0) echo ' even' ?>"
                                                data-model="<?php echo $model->getId() ?>"
                                                data-discussion="<?php echo $model->getDiscussionId() ?>"
                                                data-new-messages="<?php echo $new_messages_count ?>">
                                                <td data-sort-value="<?php echo $model->getId() ?>" style="width: 75px;">
                                                    <div class="num">№ <?php echo $model->getId() ?></div>
                                                    <div
                                                        class="date"><?php echo D::toLongRus($model->created_at) ?></div>
                                                </td>
                                                <td data-sort-value="<?php echo $model->getName() ?>" style="width: 180px;">
                                                    <div><?php echo $model->getName() ?></div>
                                                    <div class="sort"></div>
                                                </td>

                                                <td class="placement <?php echo $model->getModelType()->getIdentifier() ?>" style="width: 30px;">
                                                </td>

                                                <td style="width: 270px;">
                                                    <div class="address"><?php echo Utils::makeUrl($model->getValueByType('place')) ?></div>
                                                    <div class="address"><?php echo $model->getValueByType('period') ?></div>
                                                </td>

                                                <td width="81" data-sort-value="<?php echo $model->getCost() ?>">
                                                    <div><?php echo number_format($model->getCost(), 0, '.', ' ') ?>
                                                        руб.
                                                    </div>
                                                    <div class="sort"></div>
                                                </td>
                                                <!--<td width="181" class="darker">
                                                    <div><?php echo $model->getSpecialistActionText() ?></div>
                                                    <div class="sort"></div>
                                                </td>-->
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
                                                    <?php if ($new_messages_count > 0): ?>
                                                        <div class="message"><?php echo $new_messages_count ?></div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach;

                    ?>
                </div>
            </div>
        </div>
    </div>

<?php } ?>
