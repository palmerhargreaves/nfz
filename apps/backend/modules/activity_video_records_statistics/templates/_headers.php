<table class="table table-stripped headers-list">
    <?php
    $exists_formulas_list = array();

    foreach ($activity_video_records_statistics->getHeadersList() as $item): ?>
        <tr id="<?php echo $item->getId(); ?>">
            <td style="text-align: center;">
                <span style="font-size: 11px; ">Перетащи меня</span>
                <div
                    style="width: 98%; margin: 10px 0px; border: 1px solid #ccc; padding: 10px; display: inline-block; float: left; cursor: default; text-align: left;">
                    <a href="<?php echo url_for('activity_video_records_statistics_headers/edit/?id=' . $item->getId()) ?>"><?php echo $item->getHeader() ?></a>

                    <ul class="sf_admin_actions" style="float: right;">
                        <li class="sf_admin_action_copy">
                            <a href="<?php echo url_for('activity_video_records_statistics_headers/delete/?id=' . $item->getId()) ?>"
                               onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
                        </li>
                    </ul>

                    <div class="content">
                        <ul class="sf_admin_actions" style="text-align: center;">
                            <li class="sf_admin_action_new"><a
                                    href="<?php echo url_for('activity_video_records_statistics_headers_groups/new?parent_id=' . $item->getId() . '&activity_id=' . $activity_video_records_statistics->getActivityId()) ?>">Добавить
                                    группу</a></li>

                            <li class="sf_admin_action_new"><a
                                    href="<?php echo url_for('activity_video_records_statistics_headers_fields/new?parent_id=' . $item->getId() . '&activity_id=' . $activity_video_records_statistics->getActivityId()) ?>">Добавить
                                    поле </a></li>

                            <li class="sf_admin_action_new"><a
                                    href="<?php echo url_for('activity_efficiency_formulas/new?activity_id=' . $activity_video_records_statistics->getActivityId()) ?>">Добавить
                                    формулу эффективности </a></li>
                        </ul>

                        <?php $groups = $item->getGroupList(); ?>
                        <?php if (count($groups) > 0): ?>
                            <div style="float: left; width: 100%;">
                                <hr style="margin:5px 0px; "/>

                                <strong>Список групп</strong>
                                <table class="table" style="float: left;">
                                    <?php foreach ($groups as $group): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo url_for('activity_video_records_statistics_headers_groups/edit/?id=' . $group->getId()) ?>"><?php echo $group->getHeader() ?></a>
                                            </td>
                                            <td style="text-align: right;">
                                                <ul class="sf_admin_actions">
                                                    <li class="sf_admin_action_delete">
                                                        <a href="<?php echo url_for('activity_video_records_statistics_headers_groups/delete/?id=' . $group->getId()) ?>"
                                                           onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
                                                    </li>
                                                </ul>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php $fields = $item->getFieldsList(); ?>
                        <?php if (count($fields) > 0): ?>
                            <div style="float: left; width: 100%;">
                                <hr style="margin:5px 0px; "/>

                                <strong>Список полей</strong>
                                <table class="table header-fields-list" style="float: left;">
                                    <thead style="font-weight: bold;">
                                    <tr>
                                        <td style="width: 70%;">Поле</td>
                                        <td>Группа</td>
                                        <td style="text-align: right;">Действие</td>
                                    </tr>
                                    </thead>
                                    <?php foreach ($fields as $item_data): ?>
                                        <tr id="<?php echo $item_data->getId(); ?>">
                                            <td>
                                                <a href="<?php echo url_for('activity_video_records_statistics_headers_fields/edit/?id=' . $item_data->getId()) ?>"><?php echo $item_data->getName() ?></a>
                                            </td>
                                            <td><?php echo $item_data->getGroupName(); ?></td>
                                            <td style="text-align: right;">
                                                <ul class="sf_admin_actions">
                                                    <li class="sf_admin_action_delete">
                                                        <a href="<?php echo url_for('activity_video_records_statistics_headers_fields/delete/?id=' . $item_data->getId()) ?>"
                                                           onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
                                                    </li>
                                                </ul>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php $efficiency_formulas = ActivityEfficiencyFormulasTable::getInstance()
                            ->createQuery('f')
                            ->innerJoin('f.ActivityEfficiencyWorkFormulas pf')
                            ->where('activity_id = ?', $activity_video_records_statistics->getActivityId())
                            ->orderBy('pf.position ASC')
                            ->execute(); ?>
                        <?php if (count($efficiency_formulas) > 0 && count($exists_formulas_list) == 0): ?>
                            <div style="float: left; width: 100%;">
                                <hr style="margin:5px 0px; "/>

                                <strong>Список формул еффективности</strong>
                                <table class="table header-fields-list" style="float: left;">
                                    <thead style="font-weight: bold;">
                                    <tr>
                                        <td style="width: 30%;">Название</td>
                                        <td>Описание</td>
                                        <td style="width: 30%;">Параметры</td>
                                        <td style="text-align: right;">Действие</td>
                                    </tr>
                                    </thead>
                                    <?php foreach ($efficiency_formulas as $param):  ?>
                                        <?php $exists_formulas_list[] = $param->getId(); ?>
                                        <tr id="<?php echo $param->getId(); ?>">
                                            <td>
                                                <a href="<?php echo url_for('activity_efficiency_formulas/edit/?id=' . $param->getId()) ?>">
                                                    <?php echo $param->getName(); ?>
                                                </a>
                                            </td>

                                            <td><?php echo $param->getDescription(); ?></td>
                                            <td>
                                                <?php
                                                $params_list = ActivityEfficiencyFormulaParamsTable::getInstance()->createQuery()->where('formula_id = ?', $param->getId())->execute();
                                                if (count($params_list) > 0):
                                                    ?>
                                                    <table cellspacing="0" style="width: 100%;">
                                                        <thead>
                                                        <tr>
                                                            <th>
                                                                Параметры формулы
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td style="">
                                                                <ul class="sf_admin_actions">
                                                                    <?php
                                                                    foreach ($params_list as $param_data):
                                                                        ?>
                                                                        <li>
                                                                            <a href="<?php echo url_for('activity_efficiency_formulas_params/edit/?id=' . $param_data->getId().'&formula_id='.$param->getId()) ?>">
                                                                                <?php echo $param_data->getParamsLabels(); ?></a>
                                                                            <ul>
                                                                                <li class="sf_admin_action_delete">
                                                                                    <a href="<?php echo url_for('activity_efficiency_formulas_params/delete/?id=' . $param_data->getId()) ?>"
                                                                                       onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                <?php endif; ?>
                                                <ul class="sf_admin_actions" style="text-align: center;">
                                                    <li class="sf_admin_action_new"><a
                                                            href="<?php echo url_for('activity_efficiency_formulas_params/new?formula_id=' . $param->getId()) ?>">Добавить
                                                            параметры </a></li>
                                                </ul>
                                            </td>
                                            <td style="text-align: right;">
                                                <ul class="sf_admin_actions">
                                                    <li class="sf_admin_action_delete">
                                                        <a href="<?php echo url_for('activity_efficiency_formulas/delete/?id=' . $param->getId()) ?>"
                                                           onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
                                                    </li>
                                                </ul>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<ul class="sf_admin_actions">
    <li class="sf_admin_action_new"><a
            href="<?php echo url_for('activity_video_records_statistics_headers/new?parent_id=' . $activity_video_records_statistics->getId()) ?>">Добавить
            заголовок</a>
    </li>
</ul>

<script>
    $(function () {
        new ActivityFieldsReorder({
            on_reorder_fields: '<?php echo url_for('@activity_fields_reorder'); ?>',
            on_reorder_headers: '<?php echo url_for('@activity_headers_reorder'); ?>'
        }).start();
    });
</script>
