<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 15.09.2015
 * Time: 12:01
 */
?>

<ul class="nav nav-tabs" id="blocked-models-tabs">
    <?php
    $currYear = D::getYear(time());
    foreach ($models['models'] as $year => $model):
        ?>
        <li class="<?php echo $currYear == $year ? "active" : ""; ?>">
            <a href="#year-<?php echo $year; ?>" data-toggle="tab">
                <?php echo sprintf("Год: %s (заблокировано - %s)", $year, count($model)); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content">
    <?php foreach ($models['models'] as $year => $models): ?>
        <div class="tab-pane <?php echo $currYear == $year ? "active" : ""; ?>" id="year-<?php echo $year; ?>">

            <table class="table table-hover  table-striped">
                <thead>
                <tr>
                    <th style='width: 1%;'>№</th>
                    <th style="width: 35%;">Заявка</th>
                    <th>Текущий статус</th>
                    <th>Дата блокировки</th>
                    <th>Последние изменения</th>
                    <th>События</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                <?php
                foreach ($models as $model_item):
                    $model = $model_item['model'];
                    ?>
                    <tr>
                        <td>
                            <?php if ($model_item['blocked_info']['total_unblock_count'] > 0): ?>
                                <a href="javascript:;" class="action-show-model-blocked-info" data-model-id="<?php echo $model['id']; ?>"><?php echo $model['id']; ?></a>
                            <?php else: ?>
                                <?php echo $model['id']; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $model['name']; ?></td>
                        <td><?php echo $model['is_blocked'] && !$model['allow_use_blocked'] ? '<span class="label label-waring">Заблокирована</span>' : '<span class="label label-success">Разблокирована</span>'; ?></td>
                        <td><?php echo $model_item['blocked_info']['created_at']; ?></td>
                        <td><?php echo $model_item['blocked_info']['updated_at']; ?></td>
                        <td>
                            <?php
                                echo sprintf('Блокировок: %d</br>', $model_item['blocked_info']['total_blocked_count']);
                                echo sprintf('Разблокировок: %d</br>', $model_item['blocked_info']['total_unblock_count']);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>
