<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Параметры активностей (сервисные акции)</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <div class="alert alert-<?php echo count($activitiesSettings) > 0 ? "success" : "error"; ?> container-success" style="">
                    <?php if(count($activitiesSettings) > 0): ?>
                        Всего найдено сервисных акций: <?php echo count($activitiesSettings) ;?>
                    <?php else: ?>
                        Ничего не найдено
                    <?php endif; ?>
                </div>

                <form action="activity_statistic_settings">
                    <ul class="nav nav-list">
                        <li class="nav-header">Фильтр по дилерам</li>
                        <li>
                            Дилер:<br/>
                            <select id="sbDealer" name="sbDealer">
                                <option value="-1">Выберите дилера ...</option>
                                <?php foreach($dealers as $dealer):
                                    $sel = isset($dealerFilter) && $dealerFilter == $dealer->getId() ? "selected" : "";
                                    ?>
                                    <option value="<?php echo $dealer->getId(); ?>" <?php echo $sel; ?>><?php echo sprintf('[%s] %s', $dealer->getNumber(), $dealer->getName()); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                        <li>
                            <input type="submit" id="btDoFilterData" class="btn" style="margin-top: 15px;" value="Фильтр" />
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>

    <div id="dealer-activities-list" class="row-fluid">
        <?php include_partial('activities_list', array('activitiesSettings' => $activitiesSettings)); ?>
    </div>
</div>

<div class="modal hide fade model-history-modal" id="activity-settings-modal" style="width: 500px; left: 45%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Параметры активности (Сервисные акции)</h4>
    </div>
    <div class="modal-body">
        <div class="modal-content-container" style="width: 100%; float:left;"></div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
    </div>
</div>

<script type="text/javascript">
    window.activitySettings = new ActivitySettings({
        modal: '#activity-settings-modal',
        show_url : '<?php echo url_for('activity_settings_show'); ?>',
        apply_url: '<?php echo url_for('activity_settings_apply_data'); ?>'
    }).start();
</script>