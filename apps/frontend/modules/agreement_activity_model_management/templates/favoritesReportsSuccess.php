<?php include_partial('modal_model', array('decline_reasons' => $decline_reasons, 'decline_report_reasons' => $decline_report_reasons, 'specialist_groups' => $specialist_groups)) ?>
<?php include_partial('menu', array('active' => 'favorites', 'budYears' => $budgetYears, 'year' => $year, 'url' => 'agreement_module_management_models')) ?>
<div class="approvement" style="min-height: 55px;">
    <div style="display: block; width: 100%; float: left; margin-bottom: 20px;">
        <div style='float:left; width: 25%;'>
            <h1>Отложенные отчеты</h1>
        </div>

        <div style='float:left; width: 75%;'>
            <div id="filters" style="left: 180px;">
                <form action="<?php echo url_for('@favorites_reports') ?>" method="get">

                    <div class="modal-select-wrapper krik-select select dealer filter">
                        <?php if ($favorites_dealer_filter): ?>
                            <span class="select-value"><?php echo $favorites_dealer_filter->getRawValue() ?></span>
                            <input type="hidden" name="dealer_id"
                                   value="<?php echo $favorites_dealer_filter->getId() ?>">
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
                               value="<?php echo $favorites_start_date_filter ? date('d.m.Y', $favorites_start_date_filter) : '' ?>"
                               class="with-date"/>
                    </div>
                    <div class="date-input filter">
                        <input type="text" placeholder="до" name="end_date" class="with-date"
                               value="<?php echo $favorites_end_date_filter ? date('d.m.Y', $favorites_end_date_filter) : '' ?>"/>
                    </div>

                    <div class="modal-select-wrapper krik-select select dealer filter"
                         style="margin-left: 10px; clear: both; width: 250px;">
                        <?php if ($favorites_activity_filter): ?>
                            <span
                                class="select-value"><?php echo sprintf('%s - %s', $favorites_activity_filter->getId(), $favorites_activity_filter->getRawValue()); ?></span>
                            <input type="hidden" name="activity_id"
                                   value="<?php echo $favorites_activity_filter->getId() ?>">
                        <?php else: ?>
                            <span class="select-value">Текущие активности</span>
                            <input type="hidden" name="activity_id">
                        <?php endif; ?>
                        <div class="ico"></div>
                        <span class="select-filter"><input type="text"></span>
                        <div class="modal-input-error-icon error-icon"></div>
                        <div class="error message"></div>
                        <div class="modal-select-dropdown">
                            <div class="modal-select-dropdown-item select-item" data-value="">Все</div>
                            <?php foreach ($activities as $activity): ?>
                                <div class="modal-select-dropdown-item select-item" style="height: 32px;"
                                     data-value="<?php echo $activity->getId() ?>"><?php echo sprintf('%s - %s', $activity->getId(), $activity->getName()); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="modal-select-wrapper krik-select select dealer filter"
                         style="margin-left: 10px; width: 250px;">
                        <?php if ($favorites_activity_finished_filter): ?>
                            <span
                                class="select-value"><?php echo sprintf('%s - %s', $favorites_activity_finished_filter->getId(), $favorites_activity_finished_filter->getRawValue()); ?></span>
                            <input type="hidden" name="finished_activity_id"
                                   value="<?php echo $favorites_activity_finished_filter->getId() ?>">
                        <?php else: ?>
                            <span class="select-value">Завершенные активности</span>
                            <input type="hidden" name="finished_activity_id">
                        <?php endif; ?>
                        <div class="ico"></div>
                        <span class="select-filter"><input type="text"></span>
                        <div class="modal-input-error-icon error-icon"></div>
                        <div class="error message"></div>
                        <div class="modal-select-dropdown">
                            <div class="modal-select-dropdown-item select-item" data-value="">Все</div>
                            <?php foreach ($finishedActivities as $activity): ?>
                                <div class="modal-select-dropdown-item select-item" style="height: 32px;"
                                     data-value="<?php echo $activity->getId() ?>"><?php echo sprintf('%s - %s', $activity->getId(), $activity->getName()); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="modal-select-wrapper krik-select select type filter"
                         style="margin-left: 10px; width: 160px;">
                        <div class="ico"></div>

                        <?php if ($favorites_model_type_filter): ?>
                            <span class="select-value"><?php echo $favorites_model_type_filter->getName(); ?></span>
                            <input type="hidden" name="model_type"
                                   value="<?php echo $favorites_model_type_filter->getId() ?>">
                        <?php else: ?>
                            <span class="select-value">Все</span>
                            <input type="hidden" name="model_type">
                        <?php endif; ?>

                        <div class="ico"></div>
                        <span class="select-filter"><input type="text"></span>
                        <div class="modal-input-error-icon error-icon"></div>
                        <div class="error message"></div>
                        <div class="modal-select-dropdown">
                            <div class="modal-select-dropdown-item select-item" data-value="">Все</div>
                            <?php foreach ($modelTypes as $type): ?>
                                <div class="modal-select-dropdown-item select-item"
                                     data-value="<?php echo $type->getId() ?>"><?php echo $type->getName(); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>


                </form>
            </div>
        </div>

        <div style='float: right' class='small button favorites-to-archive'>Выгрузить</div>
        <div style='float: right; display: none;' class='small button favorites-to-pdf'>Сформировать отчет</div>
        <img src="/images/loader.gif" class="favorites-reports-items-loader" style="float: right; display: none"/>

    </div>

    <br/>
    <div id="agreement-models">
        <?php if (count($favorites) > 0): ?>
            <br/>
            <h2>Отчеты</h2>

            <?php if (isset($paginatorData)): ?>
                <table width="100%" style="margin-bottom: 10px;">
                    <tr>
                        <td><?php include_partial('global/paginator', $paginatorData); ?></td>
                    </tr>
                </table>
            <?php endif; ?>

            <table class="models" id="models-list">
                <thead>
                <tr>
                    <td width="1"><a href="javascript:;" title="Выбрать / Снять"
                                     class="ch-favorite-report-items-check-uncheck">#</a<</td>
                    <td width="170">Название активности</td>
                    <td width="75" style='text-align: center;'>Тип размещения</td>
                    <td width="75" style='text-align: center;'>Номер заявки</td>
                    <td width="120">Дилер</td>
                    <td width="75" style='text-align: center;'>Дата загрузки отчета</td>
                    <td width="150">Файл</td>
                    <td width="50">Действия</td>
                </tr>
                </thead>
                <tbody>
                <?php
                include_partial('favorites_items', array('favorites' => $favorites));
                ?>
                </tbody>
            </table>

            <?php if (isset($paginatorData)): ?>
                <table width="100%" style="margin-top: 10px;">
                    <tr>
                        <td><?php include_partial('global/paginator', $paginatorData); ?></td>
                    </tr>
                </table>
            <?php endif; ?>

        <?php endif; ?>
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

        window.favoritesOrders = new AgreementModelReportFavoritesManagementController({
            add_to_archive: "<?php echo url_for('agreement_model_report_favorites_add_to_archive'); ?>",
            delete_favorite_item: "<?php echo url_for('agreement_model_report_favorites_delete_item'); ?>"
        }).start();

        window.favorites_report_to_pdf = new FavoritesReports({
            export_url: '<?php echo url_for('favorites_reports_export_to_pdf'); ?>'
        }).start();


    });
</script>
