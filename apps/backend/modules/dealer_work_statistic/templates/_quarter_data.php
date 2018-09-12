<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 15.02.2016
 * Time: 15:01
 */
?>

<?php $qData = $item['quarters']['q' . $q]; ?>
<span style="float: left; display: block;width: 100%;"><?php echo number_format($qData['value'], 0, '.', ' ') ?> руб.</span>
<?php
if ($qData['diff'] != 0) { ?>
    <span class="badge badge-<?php echo $qData['has_changes'] == 'positive' ? 'success' : 'warning'; ?>" style="font-size: 10px; height: 12px;">
    <?php
        echo $qData['has_changes'] == 'positive' ? '+' : '-';
        echo number_format($qData['diff'], 0, '.', ' ')
        ?> руб.
    </span>
    <?php
}
?>
