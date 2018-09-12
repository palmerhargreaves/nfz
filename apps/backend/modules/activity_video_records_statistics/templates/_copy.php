<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 19.08.2016
 * Time: 9:57
 */

if (ActivityEfficiencyFormulasTable::getInstance()->createQuery()->where('activity_id = ?', $activity_video_records_statistics->getActivityId())->count() > 0):
    $acts_ids[] = $activity_video_records_statistics->getActivityId();

    /*$fields = ActivityFieldsTable::getInstance()->createQuery()->select('activity_id')->groupBy('activity_id')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    $acts_ids = array_map(function ($field) {
        return $field['activity_id'];
    }, $fields);*/

    $activities = ActivityTable::getInstance()->createQuery()->whereNotIn('id', $acts_ids)/*->andWhere('finished = ?', false)*/->orderBy('id DESC')->execute();
    ?>
    <select class="sb-copy-to-activities-<?php echo $activity_video_records_statistics->getActivityId(); ?>" multiple size="10">
        <?php foreach ($activities as $activity): ?>
            <option
                value="<?php echo $activity->getId(); ?>"><?php echo sprintf('[%s] %s', $activity->getId(), $activity->getName()); ?></option>
        <?php endforeach; ?>
    </select>

    <button class="bt-on-copy-activity-statistic" class="btn btn-success" data-from-activity="<?php echo $activity_video_records_statistics->getActivityId(); ?>">Копировать</button>
    <button class="bt-on-custom-copy-activity-statistic" class="btn btn-success" data-from-activity="<?php echo $activity_video_records_statistics->getActivityId(); ?>">Выборочное копирование</button>
<?php endif; ?>
