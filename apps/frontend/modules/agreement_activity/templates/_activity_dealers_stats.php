<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 18.05.2016
 * Time: 10:07
 */
?>

<div class="d-legend odd"><span>Статистика за: <?php echo $year; ?>г.</span></div>
<table class="tbl-common tbl-common-odd tbl-common-custom">
    <tbody>
    <tr>
        <td width="50%">Всего дилеров: <?php echo $total_dealers; ?></td>
        <td width="50%">Макетов: <?php echo $total_models; ?></td>
    </tr>
    <tr>
        <td width="50%">Всего в работе: <?php echo $in_work; ?></td>
        <td width="50%">Выполнено: <?php echo $models_completed; ?></td>
    </tr>
    <tr>
        <td width="50%">Выполнили: <?php echo $completed; ?></td>
        <td width="50%">Макет согласован: <?php echo $models_in_work; ?></td>
    </tr>
    <tr>
        <td width="50%">Макет добавлен: <?php echo $not_work; ?></td>
        <td width="50%"></td>
    </tr>
    </tbody>
</table>
