<?php

$k = 0;
$isBlocked = false;
foreach ($favorites as $item):
    $report = $item->getReport();
    $model = $report->getModel();
    $activity = $model->getActivity();

    ?>
    <tr class='favorite-item-<?php echo $item->getId(); ?>'>
        <td><input type="checkbox" class="ch-check-uncheck-fav-report-item"
                   id="chFavoriteReportItem<?php echo $item->getId(); ?>"
                   name="chFavoriteReportItem<?php echo $item->getId(); ?>" data-id="<?php echo $item->getId(); ?>"/>
        </td>
        <td><?php echo sprintf('%s - %s', $activity->getId(), $activity->getName()); ?></td>
        <td style='text-align: center;'><?php echo $model->getModelType()->getName(); ?></td>
        <td style='text-align: center;'><?php echo $model->getId(); ?></td>
        <td><?php echo $model->getDealer()->getName() ?></td>
        <td style='text-align: center;'><?php echo D::toDb($item['report_added']); ?></td>
        <td>
            <a href="/uploads/<?php echo AgreementModelReport::ADDITIONAL_FILE_PATH . '/' . $item->getFileName() ?>"
               target="_blank"><?php echo $item->getFileName(), ' (', $report->getAdditionalFileNameHelper()->getSmartSize() . ')' ?></a>
        </td>
        <td style='text-align: center;'><img src='/images/delete-icon.png' class='delete-favorite-item'
                                             data-id='<?php echo $item->getId(); ?>' title='Удалить'
                                             style='cursor: pointer'/></td>
    </tr>
    <?php $k++; endforeach; ?>
