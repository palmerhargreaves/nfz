<?php //include_partial('modal_model', array('decline_reasons' => $decline_reasons, 'decline_report_reasons' => $decline_report_reasons, 'specialist_groups' => $specialist_groups, 'outOfDate' => $outOfDate)) ?>

<div class="approvement">
    <h1>Мои заявки</h1>
    <?php
    $status_filter = array(
        'all' => 'Все',
        'in_work' => 'На проверке',
        'complete' => 'Завершенные',
        'process_draft' => 'Черновик',
        'process_reports' => 'Отчеты',
        'current' => 'Текущие',
        'blocked' => 'Заблокированные'

    );
    ?>
    <div id="filters" style='left: 175px;'>
        <form action="<?php echo url_for('@agreement_module_model_activities') ?>" method="get">
            <div class="modal-select-wrapper krik-select select type filter">
                <span class="select-value"><?php echo isset($status_filter[$sf_data->getRaw('activity_status')]) ? $status_filter[$sf_data->getRaw('activity_status')] : ''; ?></span>
                <div class="ico"></div>
                <input type="hidden" name="activity_status" value="<?php echo $activity_status ?>">
                <div class="modal-input-error-icon error-icon"></div>
                <div class="error message"></div>
                <div class="modal-select-dropdown">
                    <?php foreach ($status_filter as $value => $name): ?>
                        <div class="modal-select-dropdown-item select-item"
                             data-value="<?php echo $value ?>"><?php echo $name ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="date-input filter">
                <input type="text" placeholder="от" name="start_date"
                       value="<?php echo $start_date_filter ? date('d.m.Y', $start_date_filter) : '' ?>"
                       class="with-date"/>
            </div>
            <div class="date-input filter">
                <input type="text" placeholder="до" name="end_date" class="with-date"
                       value="<?php echo $end_date_filter ? date('d.m.Y', $end_date_filter) : '' ?>"/>
            </div>
        </form>
    </div>

    <div id="agreement-models">
        <div id="materials" class="active" style="padding-top: 7px;">
            <?php if (count($models) > 0): ?>
                <?php $k = 0;
                foreach ($models as $year => $data):
                    $header = sprintf('Заявки за %s', $year);
                    $summ = '';

                    if ($sf_data->getRaw('activity_status') == 'all' || $sf_data->getRaw('activity_status') == 'complete')
                        $summ = number_format($data['summ'], 0, '.', ' ') . ' руб.';
                    ?>
                    <div class="group <?php echo $year == date('Y') ? 'open' : '' ?>">
                        <div class="group-header">
                            <span class="title"><?php echo $header; ?></span>
                            <div class="summary"><?php echo $summ; ?></div>
                            <div class="group-header-toggle"></div>
                        </div>
                        <div class="group-content">
                            <table class="models" id="models-list">
                                <thead>
                                <tr>
                                    <td width="75">
                                        <div class="has-sort">ID / Дата</div>
                                        <div class="sort has-sort"></div>
                                    </td>
                                    <td width="146">
                                        <div class="has-sort">Дилер</div>
                                        <div class="sort has-sort"></div>
                                    </td>
                                    <td width="180">
                                        <div class="has-sort">Название</div>
                                        <div class="sort has-sort"></div>
                                    </td>
                                    <!--<td width="146"><div>Размещение</div></td>-->
                                    <td width="105">
                                        <div>Период</div>
                                    </td>
                                    <td width="81">
                                        <div class="has-sort">Сумма</div>
                                        <div class="sort has-sort" data-sort="cost"></div>
                                    </td>
                                    <td>
                                        <div>Действие</div>
                                    </td>
                                    <?php if ($activity_status && ($activity_status == 'in_work' || $activity_status == 'all')) { ?>
                                        <td width="100">
                                            <div>На проверке до</div>
                                        </td>
                                    <?php } else if ($activity_status && ($activity_status == 'process_reports' || $activity_status == 'blocked')) { ?>
                                        <td width="100">
                                            <div>Загрузить до</div>
                                        </td>
                                    <?php } ?>

                                    <td width="35">
                                        <div>Макет</div>
                                    </td>
                                    <td width="35">
                                        <div>Отчет</div>
                                    </td>
                                    <td width="35">
                                        <div>
                                            <div class="has-sort">&nbsp;</div>
                                            <!--div class="sort has-sort" data-sort="messages"></div--></div>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                foreach ($data['data'] as $item):
                                    $model = $item['model'];
                                    $dateText = date('H:i d-m-Y', $item['date']);

                                    $isDealer = $sf_user->isDealerUser();
                                    if ($sf_user->isImporter()) {
                                        $isDealer = false;
                                    }

                                    $model_date = $model->getModelQuarterDate();

                                    ?>
                                    <?php $discussion = $model->getDiscussion() ?>
                                    <?php $new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0 ?>
                                    <tr class="sorted-row model-row<?php if ($k % 2 == 0) echo ' even' ?> dummy"
                                        data-activity-id="<?php echo $model->getActivityId(); ?>"
                                        data-model="<?php echo $model->getId() ?>"
                                        data-discussion="<?php echo $model->getDiscussionId() ?>"
                                        data-new-messages="<?php echo $new_messages_count ?>"
                                        data-model-quarter="<?php echo D::getQuarter($model_date); ?>"
                                        data-model-year="<?php echo D::getYear($model_date); ?>">
                                        <td data-sort-value="<?php echo $model->getId() ?>">
                                            <div
                                                style='float:left; width: 12px;'><?php echo $item['status'] ? '<img src="/images/hOpened.gif" title="Переход по годам" />' : ''; ?></div>
                                            <div class="num">№ <?php echo $model->getId() ?></div>
                                            <div class="date"><?php echo D::toLongRus($model->created_at) ?></div>
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
                                            <div><?php echo $model->getDealerActionText() ?></div>
                                            <div class="sort"></div>
                                        </td>

                                        <?php if ($activity_status && $activity_status == 'in_work') { ?>
                                            <?php if ($model->getCssStatus() != 'ok') { ?>
                                                <td class="darker"
                                                    style="<?php echo $model->isModelAcceptActiveToday($isDealer) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                                                    <div><?php echo $dateText; ?></div>
                                                    <div class="sort"></div>
                                                </td>
                                            <?php } else {
                                                if ($model->getReport() && ($model->getReport()->getStatus() != "accepted" && $model->getReport()->getStatus() != "not_sent")): ?>
                                                    <td class="darker"
                                                        style="<?php echo $model->isModelAcceptActiveToday($isDealer) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                                                        <div><?php echo $dateText; ?></div>
                                                        <div class="sort"></div>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="darker">
                                                        <div class="sort"></div>
                                                    </td>
                                                <?php endif; ?>
                                            <?php } ?>
                                        <?php } else if ($activity_status && $activity_status == 'all') {
                                            if ($model->getCssStatus() == 'clock') {
                                                ?>
                                                <td class="darker"
                                                    style="<?php echo $model->isModelAcceptActiveToday($isDealer) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                                                    <div><?php echo $dateText; ?></div>
                                                    <div class="sort"></div>
                                                </td>
                                            <?php } else {
                                                if ($model->getReport() && ($model->getReport()->getStatus() != "accepted" && $model->getReport()->getStatus() != "not_sent")): ?>
                                                    <td class="darker"
                                                        style="<?php echo $model->isModelAcceptActiveToday($isDealer) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                                                        <div><?php echo $dateText; ?></div>
                                                        <div class="sort"></div>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="darker">
                                                        <div class="sort"></div>
                                                    </td>
                                                <?php endif; ?>
                                            <?php } ?>
                                        <?php } else if ($activity_status && ($activity_status == 'process_reports' || $activity_status == 'blocked')) { ?>
                                            <td class="darker"
                                                style="<?php echo $model->isModelAcceptActiveToday($isDealer, true) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                                                <div><?php echo $dateText; ?></div>
                                                <div class="sort"></div>
                                            </td>
                                        <?php } ?>

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
                                    <?php $k++; endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php
                endforeach;
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(function () {
        new TableSorter({
            selector: '#models-list'
        }).start();

        $('#filters form :input[name]').change(function () {
            this.form.submit();
        });

        $('#filters form .with-date').datepicker();

        /*$('#models-list .dummy').live('click', function () {
            window.open('/activity/' + $(this).data('activity-id') + '/module/agreement/models/model/' + $(this).data('model'), '_blank');
        });*/

        $('#models-list .dummy').live('click', function () {
            window.open('/activity/' + $(this).data('activity-id') +
                '/module/agreement/models/model/' + $(this).data('model') +
                '/quarter/' + $(this).data('model-quarter') +
                '/year/' + $(this).data('model-year')
                , '_blank');
        });
    });
</script>
