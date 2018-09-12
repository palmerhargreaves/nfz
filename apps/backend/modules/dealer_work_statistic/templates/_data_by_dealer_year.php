<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 15.02.2016
 * Time: 14:30
 */
?>

<?php

$dealer = DealerTable::getInstance()->find($filterDealer);

echo $dealer->getShortNumber();
echo $dealer->getName();

$result = array();
$prevSumm = 0;

$q1 = 0;
$q2 = 0;
$q3 = 0;
$q4 = 0;

foreach ($stats as $item) {
    $newSum = false;

    $q1Diff = 0;
    $q2Diff = 0;
    $q3Diff = 0;
    $q4Diff = 0;

    $sum = $item->getQ1() + $item->getQ2() + $item->getQ3() + $item->getQ4();
    if ($sum != 0) {
        if ($prevSumm != $sum) {

            if ($q1 != $item->getQ1()) {
                if ($q1 != 0) {
                    $q1Diff = $item->getQ1() - $q1;
                }
                $q1 = $item->getQ1();
            }

            if ($q2 != $item->getQ2()) {
                if ($q2 != 0) {
                    $q2Diff = $item->getQ2() - $q2;
                }
                $q2 = $item->getQ2();
            }

            if ($q3 != $item->getQ3()) {
                if ($q3 != 0) {
                    $q3Diff = $item->getQ3() - $q3;
                }
                $q3 = $item->getQ3();
            }

            if ($q4 != $item->getQ4()) {
                if ($q4 != 0) {
                    $q4Diff = $item->getQ4() - $q4;
                }
                $q4 = $item->getQ4();
            }

            $result[] = array(
                'quarters' => array(
                    'q1' => array(
                        'value' => $item->getQ1(),
                        'diff' => $q1Diff,
                        'has_changes' => $q1Diff > 0 ? 'positive' : 'negative'
                    ),
                    'q2' => array(
                        'value' => $item->getQ2(),
                        'diff' => $q2Diff,
                        'has_changes' => $q2Diff > 0 ? 'positive' : 'negative'
                    ),
                    'q3' => array(
                        'value' => $item->getQ3(),
                        'diff' => $q3Diff,
                        'has_changes' => $q3Diff > 0 ? 'positive' : 'negative'
                    ),
                    'q4' => array(
                        'value' => $item->getQ4(),
                        'diff' => $q4Diff,
                        'has_changes' => $q4Diff > 0 ? 'positive' : 'negative'
                    ),

                )
            ,
                'sum' => $sum,
                'newSum' => $sum - $prevSumm,
                'date' => $item->getCreatedAt()
            );

            $prevSumm = $sum;
        }
    }
}

?>

<table class="table table-bordered table-striped " cellspacing="0">
    <thead>
    <tr>
        <th>1 Квартал</th>
        <th>2 Квартал</th>
        <th>3 Квартал</th>
        <th>4 Квартал</th>
        <th>Сумма</th>
        <!--<th >Активностей</th>
        <th >Моделей</th>-->
        <th>На дату</th>
    </tr>
    </thead>

    <tbody>
    <?php
        foreach ($result as $item) {
        ?>
        <tr>
            <td class="span2">
                <?php include_partial('quarter_data', array('item' => $item, 'q' => 1)); ?>
            </td>
            <td class="span2">
                <?php include_partial('quarter_data', array('item' => $item, 'q' => 2)); ?>
            </td>
            <td class="span2">
                <?php include_partial('quarter_data', array('item' => $item, 'q' => 3)); ?>
            </td>
            <td class="span2">
                <?php include_partial('quarter_data', array('item' => $item, 'q' => 4)); ?>
            </td>
            <td class="span3">
                <span style="float: left; display: block;width: 100%;"><?php echo number_format($item['sum'], 0, '.', ' ') ?> руб.</span>
                <?php if($item['sum'] != $item['newSum']) { ?>
                    <span class="badge badge-<?php echo $item['newSum'] > 0 ? 'success' : 'warning'; ?>" style="font-size: 10px; height: 12px;">
                    <?php
                        echo $item['newSum'] > 0 ? '+' : '-';
                        echo number_format($item['newSum'], 0, '.', ' ')
                    ?> руб.
                </span>
                <?php } ?>
            </td>
            <td class="span3"><?php echo $item['date']; ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
