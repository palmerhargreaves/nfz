<ul class="sf_admin_actions">
    <?php
    $items = $activity->getActivityVideoStatistics();
    foreach ($items as $item): ?>
        <li>
            <a href="<?php echo url_for('activity_video_records_statistics/edit/?id=' . $item->getId().'&activity_id='.$item->getActivityId()) ?>"><?php echo $item->getHeader() ?></a>
            <ul>
                <li class="sf_admin_action_delete">
                    <a href="<?php echo url_for('activity_video_records_statistics/delete/?id=' . $item->getId()) ?>"
                       onclick="if(confirm('Вы уверены?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', 'sf_method'); m.setAttribute('value', 'delete'); f.appendChild(m);f.submit(); }; return false; ">удалить</a>
                </li>
            </ul>
        </li>
    <?php endforeach; ?>
    <li class="sf_admin_action_new"><a
            href="<?php echo url_for('activity_video_records_statistics/new?activity_id=' . $activity->getId()) ?>">Добавить</a></li>

    <?php if (count($items) > 0): ?>
        <li class="sf_admin_action_list">
            <a href="<?php echo url_for('activity_video_records_statistics/index/?activity_id='.$activity->getId()) ?>">Список</a>
        </li>
    <?php endif; ?>
</ul>
