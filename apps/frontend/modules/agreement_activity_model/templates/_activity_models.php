<div class="content-wrapper">
    <?php include_partial('activity/activity_tabs', array('activity' => $activity, 'active' => 'agreement')) ?>

    <div class="pane-shadow"></div>
    <div id="agreement-models" class="pane clear">

        <?php if ($has_concept): ?>
            <?php include_partial('agreement_activity_model/concept', array('concept' => $concept, 'activity' => $activity)) ?>
        <?php endif; ?>

        <div id="approvement" class="active">
            <?php
            $allow_add_models = !$activity->getFinished();
            if ($allow_add_models) {
                ?>
                    <div id="add-model-button" class="lnk-button lnk-button-add">Добавить макет</div>
            <?php } ?>

            <div class="agreement-info"><p><strong>Внимание!</strong> Все заявки, размещаемые в течение квартала, должны быть заведены в период этого квартала.</p></div>

            <?php if (count($models) > 0 || count($blanks) > 0): ?>
                <table class="models">
                    <thead>
                    <tr>
                        <td width="110">
                            <div class="has-sort">ID / Дата</div>
                            <div class="sort has-sort"></div>
                        </td>
                        <td width="170">
                            <div class="has-sort">Название</div>
                            <div class="sort has-sort"></div>
                        </td>
                        <td width="">
                            <div>Размещение</div>
                        </td>
                        <td width="130">
                            <div>Период</div>
                        </td>
                        <td width="70">
                            <div class="has-sort">Сумма</div>
                            <div class="sort has-sort"></div>
                        </td>
                        <td width="110">
                            <div>Действие</div>
                        </td>
                        <td width="55">
                            <div>Макет</div>
                        </td>
                        <td width="55">
                            <div>Отчет</div>
                        </td>
                        <td width="55">
                            <div>
                                <div class="has-sort">&nbsp;</div>
                                <!--div class="sort has-sort" data-sort="messages"></div--></div>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($blanks as $n => $blank): ?>
                        <tr class="draft model-row<?php if ($n % 2 == 0) echo ' even' ?>"
                            data-blank="<?php echo $blank->getId() ?>"
                            data-type="<?php echo $blank->getModelType()->getId() ?>"
                            data-name="<?php echo $blank->getName() ?>">
                            <td class="vam">
                                <div class="num">№ ...</div>
                                <div class="date">...</div>
                            </td>
                            <td>
                                <div><?php echo $blank->getName() ?></div>
                                <div class="sort"></div>
                            </td>
                            <td title="<?php echo $blank->getModelType()->getName() ?>"
                                class="placement <?php echo $blank->getModelType()->getIdentifier() ?>">
                                <div class="address"></div>
                            </td>
                            <td align="center"></td>
                            <td align="center">
                                <div></div>
                                <div class="sort"></div>
                            </td>
                            <td>
                                <div>Нажмите, чтобы добавить макет</div>
                                <div class="sort"></div>
                            </td>
                            <td class="vam">
                                <div class="none"></div>
                            </td>
                            <td class="vam">
                                <div class="none"></div>
                            </td>
                            <td class="vam"></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php foreach ($models as $n => $model):
                        ?>
                        <?php $discussion = $model->getDiscussion() ?>
                        <?php $new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0 ?>
                        <tr class="sorted-row model-row<?php if (($n + count($blanks) % 2) % 2 == 0) echo ' even' ?><?php if ($model->getStatus() == 'not_sent') echo ' draft' ?> <?php echo $model->getId() == $modelId ? 'auto-click' : ''; ?> "
                            data-model="<?php echo $model->getId() ?>"
                            data-discussion="<?php echo $model->getDiscussionId() ?>"
                            data-new-messages="<?php echo $new_messages_count ?>">
                            <!-- TBD: Добавить класс draft, если черновик -->
                            <td class="vam" data-sort-value="<?php echo $model->getId() ?>">
                                <div class="num">№ <?php echo $model->getId() ?></div>
                                <div class="date"><?php echo D::toLongRus($model->created_at) ?></div>
                            </td>
                            <td data-sort-value="<?php echo $model->getName() ?>">
                                <div><?php echo $model->getName() ?></div>
                                <div class="sort"></div>
                            </td>
                            <td title="<?php echo $model->getModelType()->getName() ?>"
                                class="placement <?php echo $model->getModelType()->getIdentifier() ?>">
                                <div class="address" style="margin-left: 25px;">
                                    <?php if ($model->getValueByType('place')): ?><?php echo $model->getValueByType('place') ?><? else: ?>-<?php endif; ?>
                                </div>
                            </td>
                            <td align="center"><?php echo $model->getValueByType('period') ?></td>
                            <td align="center" class="wsnw" data-sort-value="<?php echo $model->getCost() ?>">
                                <div><?php echo number_format($model->getCost(), 0, '.', ' ') ?> руб.</div>
                                <div class="sort"></div>
                            </td>
                            <td>
                                <div><?php echo $model->getDealerActionText() ?></div>
                                <div class="sort"></div>
                            </td>
                            <td class="vam">
                                <div class="<?php echo $model->getCssStatus() ?>">
                                    <?php if ($model->getStatus() == 'wait_specialist') echo 'x' . $model->countWaitingSpecialists(); ?>
                                    <?php if ($model->getStatus() == 'declined' && $model->countDeclines()) echo 'x' . $model->countDeclines(); ?>
                                </div>
                            </td>
                            <?php $report = $model->getReport(); ?>
                            <td class="vam">
                                <div class="<?php echo $model->getReportCssStatus() ?>">
                                    <?php if ($report && $report->getStatus() == 'wait_specialist') echo 'x' . $report->countWaitingSpecialists(); ?>
                                    <?php if ($report && $report->getStatus() == 'declined' && $report->countDeclines()) echo 'x' . $report->countDeclines(); ?>
                                </div>
                            </td>
                            <td data-sort-value="<?php echo $new_messages_count ?>" class="vam">
                                <?php if ($new_messages_count > 0): ?>
                                    <div class="message"><?php echo $new_messages_count ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(function () {
        new TableSorter({
            selector: '#approvement table.models'
        }).start();

        $('table.models .auto-click').trigger('click');
    });
</script>
