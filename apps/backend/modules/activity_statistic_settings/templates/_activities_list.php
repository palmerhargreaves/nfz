<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 15.09.2015
 * Time: 12:01
 */
?>

<table class="table table-hover table-bordered table-striped">
    <thead>
    <tr>
        <th style='width: 1%;'>№</th>
        <th>Активность</th>
        <th>Кварталы</th>
        <th>Тип сервисной акции</th>
        <th>Открыта</th>
        <th>Выполнена</th>
    </tr>
    </thead>

    <tbody>
    <?php
    foreach($activitiesSettings as $setting):
        $totalQComplete = 0;
        $qDatas = array();

        for($qInd = 1; $qInd <= 4; $qInd++) {
            $qFunction = 'getQ'.$qInd;
            $qData = sprintf("Квартал [%s] - %s", $qInd, ($setting->$qFunction() != 0 ? '<label class="badge badge-success">Выполнен</label>' : 'Активен'));

            if($setting->$qFunction() == 1) {
                $totalQComplete++;
            }

            $qDatas[] = $qData;
        }
    ?>
        <tr class="<?php echo $totalQComplete == 4 ? 'success' : ($setting->getStatType() == 'extended' ? 'warning' : ''); ?>">
            <td><?php echo $setting->getActivity()->getId() ?></td>
            <td><?php echo $totalQComplete != 4
                    ? "<a href='javascript:;' class='action-show-activity-settings' data-activity-id='".$setting->getActivity()->getId()."' data-dealer-id='".$setting->getDealerId()."'>".$setting->getActivity()->getName()."</a>"
                    : $setting->getActivity()->getName(); ?></td>
            <td><?php echo implode('<br/>', $qDatas) ?></td>
            <td><?php echo $setting->getStatType() == 'simple' ? 'Обычная' : 'Расширенная' ; ?></td>
            <td><?php echo $setting->getAlwaysOpen() ? "Да" : "Нет"; ?> </td>
            <td><?php echo $setting->getComplete() ? "Да" .($totalQComplete == 4 ? '' : ' (частично)')  : "Нет"; ?> </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

