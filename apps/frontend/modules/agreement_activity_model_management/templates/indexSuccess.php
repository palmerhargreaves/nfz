<?php include_partial('modal_model', array('decline_reasons' => $decline_reasons, 'decline_report_reasons' => $decline_report_reasons, 'specialist_groups' => $specialist_groups)) ?>
<?php include_partial('menu', array('active' => 'agreement', 'budYears' => $budgetYears, 'year' => $year, 'url' => 'agreement_module_management_models')) ?>
<div class="approvement" style="min-height: 55px;">
    <div style="display: block; width: 100%; float: left; margin-bottom: 20px;">
        <div style='float:left; width: 25%;'>
            <h1>Согласование</h1>
        </div>
        <?php
        $wait_filter_items = array(
            'manager' => 'Менеджеры',
            'specialist' => 'Специалисты',
            'dealer' => 'Черновики',
            'agreed' => 'Согласованные',
        );

        $model_status_filter_items = array(
            'all' => 'Все',
            'accepted' => 'Согласованы',
            'wait' => 'Не обработаны',
            'comment' => 'Отклонены',
        );

        if ($sf_data->getRaw('wait_filter') == 'all')
            $model_status_filter_items = array('all' => 'Все');
        else if ($sf_data->getRaw('wait_filter') == 'specialist')
            $model_status_filter_items = array('wait' => 'Не обработаны',
                'accepted' => 'Согласованы',
                'comment' => 'Отклонены');

        if ($sf_user->isAdmin() || $sf_user->isImporter()) {
            $model_status_filter_items['blocked'] = 'Заблокированные';
            $wait_filter_items['blocked'] = 'Заблокированные';
        }

        $wait_filter_items['all'] = 'Все';

        ?>
        <div style='float:left; width: 75%;'>
            <div id="filters">
                <form action="<?php echo url_for('@agreement_module_management_models') ?>" method="get">

                    <div class="modal-select-wrapper krik-select select type filter">
                        <span
                            class="select-value"><?php echo $wait_filter_items[$sf_data->getRaw('wait_filter')] ?></span>
                        <div class="ico"></div>
                        <input type="hidden" name="wait" value="<?php echo $wait_filter ?>">
                        <div class="modal-input-error-icon error-icon"></div>
                        <div class="error message"></div>
                        <div class="modal-select-dropdown">
                            <?php foreach ($wait_filter_items as $value => $name): ?>
                                <div class="modal-select-dropdown-item select-item"
                                     data-value="<?php echo $value ?>"><?php echo $name ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-select-wrapper krik-select select dealer filter">
                        <?php if ($dealer_filter): ?>
                            <span class="select-value"><?php echo $dealer_filter->getRawValue() ?></span>
                            <input type="hidden" name="dealer_id" value="<?php echo $dealer_filter->getId() ?>">
                        <?php else: ?>
                            <span class="select-value">Все дилеры</span>
                            <input type="hidden" name="dealer_id">
                        <?php endif; ?>
                        <div class="ico"></div>
                        <span class="select-filter"><input type="text"></span>
                        <div class="modal-input-error-icon error-icon"></div>
                        <div class="error message"></div>
                        <div class="modal-select-dropdown">
                            <div class="modal-select-dropdown-item select-item" data-value="">Все</div>
                            <?php foreach ($dealers as $dealer): ?>
                                <div class="modal-select-dropdown-item select-item"
                                     data-value="<?php echo $dealer->getId() ?>"><?php echo $dealer->getRawValue() ?></div>
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
                    <div class="date-input filter">
                        <input type="text" placeholder="№ заявки" name="model" value="<?php echo $model_filter ?>"/>
                    </div>


                    <?php if ($sf_user->isManager()) {
                        if ($sf_data->getRaw('wait_filter') == 'specialist'):
                            ?>
                            <div class="modal-select-wrapper krik-select select type filter"
                                 style="margin-right: 5px; clear: left; width: 450px;">
                                <?php if ($designer_filter): ?>
                                    <span class="select-value"><?php echo $designer_filter->getRawValue() ?></span>
                                    <input type="hidden" name="designer_id"
                                           value="<?php echo $designer_filter->getId() ?>">
                                <?php else: ?>
                                    <span class="select-value">Без дизайнера</span>
                                    <input type="hidden" name="designer_id">
                                <?php endif; ?>

                                <div class="ico"></div>
                                <span class="select-filter"><input type="text"></span>
                                <div class="modal-input-error-icon error-icon"></div>
                                <div class="error message"></div>
                                <div class="modal-select-dropdown" style="height: auto;">
                                    <div class="modal-select-dropdown-item select-item" data-value="">Без дизайнера
                                    </div>
                                    <?php foreach ($designers as $designer): ?>
                                        <div class="modal-select-dropdown-item select-item"
                                             data-value="<?php echo $designer->getId() ?>"><?php echo sprintf('%s %s (%s)', $designer->getRawValue(), $designer->getSurname(), $designer->getPost()); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($sf_data->getRaw('wait_filter') == 'all' || $sf_data->getRaw('wait_filter') == 'manager'): ?>
                            <div class="modal-select-wrapper krik-select select dealer filter"
                                 style=" margin-top: 5px;margin-left: 0px;">
                                <?php if ($activity_filter): ?>
                                    <span
                                        class="select-value"><?php echo sprintf('%s - %s', $activity_filter->getId(), $activity_filter->getRawValue()); ?></span>
                                    <input type="hidden" name="activity_id"
                                           value="<?php echo $activity_filter->getId() ?>">
                                <?php else: ?>
                                    <span class="select-value">Все активности</span>
                                    <input type="hidden" name="activity_id">
                                <?php endif; ?>
                                <div class="ico"></div>
                                <span class="select-filter"><input type="text"></span>
                                <div class="modal-input-error-icon error-icon"></div>
                                <div class="error message"></div>
                                <div class="modal-select-dropdown">
                                    <div class="modal-select-dropdown-item select-item" data-value="">Все</div>
                                    <?php foreach ($activities as $activity): ?>
                                        <div class="modal-select-dropdown-item select-item"
                                             data-value="<?php echo $activity->getId() ?>"><?php echo sprintf('%s - %s', $activity->getId(), $activity->getName()); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($sf_data->getRaw('wait_filter') == 'all2'):
                            $statusFilterData = $sf_data->getRaw('model_status_filter');
                            $clsAlign = 'none';
                            $style = '';
                            if ($sf_data->getRaw('wait_filter') == 'all')
                                $style = 'margin-right: 30px; clear: right; float: right; margin-top: 5px;';
                            else if ($sf_data->getRaw('wait_filter') == 'specialist') {
                                $clsAlign = 'inherit';
                                $statusFilterData = $statusFilterData == 'all' ? 'wait' : $statusFilterData;
                            } else if ($sf_data->getRaw('wait_filter') == 'manager') {
                                $style = 'clear: both; float: right; margin-top: 5px;';
                            } else $clsAlign = 'inherit';
                            ?>
                            <div class="modal-select-wrapper krik-select select type filter"
                                 style="<?php echo !empty($style) ? $style : 'margin-right: 5px; clear: ' . $clsAlign; ?>">
                                <span
                                    class="select-value"><?php echo $model_status_filter_items[$statusFilterData] ?></span>
                                <div class="ico"></div>

                                <input type="hidden" name="model_status" value="<?php echo $model_status_filter ?>">

                                <div class="modal-input-error-icon error-icon"></div>
                                <div class="error message"></div>
                                <div class="modal-select-dropdown">
                                    <?php foreach ($model_status_filter_items as $value => $name): ?>
                                        <div class="modal-select-dropdown-item select-item"
                                             data-value="<?php echo $value ?>"><?php echo $name ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php } ?>

                    <?php
                    $model_type_filter_items = array('all' => 'Все', 'makets' => 'Согласование макетов', 'reports' => 'Согласование отчетов');
                    if ($sf_data->getRaw('wait_filter') == 'manager'):
                        $clsAlign = 'right';
                        $style = '';
                        if ($sf_data->getRaw('wait_filter') == 'manager')
                            $style = 'margin-right: 0px; clear: inherit; float: right; margin-top: 5px; width: 190px;';
                        else $clsAlign = 'inherit';
                        ?>
                        <div class="modal-select-wrapper krik-select select type filter"
                             style="<?php echo !empty($style) ? $style : "margin-right: 5px; clear: " . $clsAlign . "; width: 160px;"; ?>">
                            <span
                                class="select-value"><?php echo isset($model_type_filter_items[$sf_data->getRaw('model_type_filter')]) ? $model_type_filter_items[$sf_data->getRaw('model_type_filter')] : 'Все'; ?></span>
                            <div class="ico"></div>

                            <input type="hidden" name="model_type" value="<?php echo $model_type_filter; ?>">

                            <div class="modal-input-error-icon error-icon"></div>
                            <div class="error message"></div>
                            <div class="modal-select-dropdown">
                                <?php foreach ($model_type_filter_items as $value => $name): ?>
                                    <div class="modal-select-dropdown-item select-item"
                                         data-value="<?php echo $value ?>"><?php echo $name ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </form>
            </div>
        </div>
    </div>

    <br/>

    <div id="agreement-models" data-url='<?php echo url_for('@agreement_module_management_model_unblock'); ?>'>
        <?php
        if (!$designer_filter && !($sf_data->getRaw('model_status_filter') == 'blocked') && !($sf_data->getRaw('wait_filter') == 'blocked'))
            include_partial('concepts', array(
                'wait_filter' => $wait_filter,
                'concepts' => $concepts,
            ));
        ?>

        <?php if (count($models) > 0): ?>
            <br/>
            <h2>Макеты</h2>
            <table class="models" id="models-list">
                <thead>
                <tr>
                    <td width="1%">
                        <div class="has-sort">№</div>
                        <div class="sort has-sort"></div>
                    </td>
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
                    <?php if ($sf_user->isManager()) { ?>
                        <td width="100">
                            <div>Согласовать до</div>
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
                include_partial('models_items', array('models' => $models,
                    'wait_filter' => $wait_filter,
                    'model_status_filter' => $model_status_filter,
                    'model_filter' => $model_filter,
                    'designer_filter' => $designer_filter));
                ?>
                </tbody>
            </table>

        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    var isLoading = false;

    $(function () {
        new TableSorter({
            selector: '#models-list'
        }).start();

        $('#filters form :input[name]').change(function () {
            this.form.submit();
        });

        $('#filters form .with-date').datepicker();

        window.reportFavorites = new AgreementModelReportFavoritesManagementController({
            add_to_favorites_url: "<?php echo url_for('agreement_module_report_add_to_favorites'); ?>",
            remove_to_favorites_url: "<?php echo url_for('agreement_module_report_remove_from_favorites'); ?>"
        }).start();
    });
</script>

