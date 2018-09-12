<?php if($is_finished): ?>
	<div class="inner-page-title"><h1>Завершенные Активности</h1></div>
<?php else: ?>
	<div class="inner-page-title"><h1>Активность</h1></div>
<?php endif; ?>

<?php
    $years = array(2013, 2014, 2015,2016);
?>
<?php include_partial('agreement_activity_model_management/menu', array('active' => 'activities', 'year' => $year, 'url' => 'agreement_module_activities', 'budYears' => $budgetYears)) ?>

<div class="activities">

<div id="dealer-list" class="modal">
    <div class="modal-header">Список дилеров</div>
    <div class="modal-close"></div>
    <div class="modal-text">Весення сервисная акция. Не приступали.
        <ul>
            <li>Дилер (000)</li>
            <li>Дилер (000)</li>
            <li>Дилер (000)</li>
            <li>Дилер (000)</li>
            <li>Дилер (000)</li>
            <li>Дилер (000)</li>
            <li>Дилер (000)</li>
        </ul>
    </div>
</div>

<?php if(count($activities) > 0): ?>
    <?php if($is_finished): ?>
    <div id="chBudYears" class="modal-select-wrapper select input krik-select float-left" style="height: 23px; padding-bottom: 1px; padding-right: 18px; width: 140px; margin-right: 10px; margin-top: 10px;">
        <span class="select-value">Активности на <?= $year; ?> г.</span>

        <div class="ico"></div>
        <input type="hidden" name="year" id="year" value="<?php echo $year ?>">

        <div class="modal-input-error-icon error-icon"></div>
        <div class="error message"></div>
        <div class="modal-select-dropdown">
        <?php foreach ($years as $y): ?>
            <?php $url = url_for("/activity/module/agreement/activities/finished?year=" . $y); ?>
            <div style='height:auto; padding: 7px;' class="modal-select-dropdown-item select-item"
                 data-year="<?= $y; ?>"
                 data-url="<?php echo $url ?>"><?= "Активности на " . $y . " г."; ?></div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <table class="tbl-common tbl-common-odd" id="activities_table">
        <tr>
            <th>Активность</th>
            <th width="17%">Сроки</th>
            <th width="17%">Выполнено</th>
            <th width="17%">В работе</th>
            <th width="17%">Не приступали</th>
        </tr>
  <?php foreach($activities as $id => $activity): ?>
        <tr>
            <td><a href="<?php echo url_for('@agreement_module_activity?id='.$id.'&year='.$year) ?>"><?php echo $activity['activity']->getName(); ?></a></td>
            <td>
    <?php if($activity['activity']->getCustomDate()): ?>
                            <?php echo nl2br($activity['activity']->getCustomDate()) ?>
    <?php else: ?>
                            с <?php echo D::toLongRus($activity['activity']->getStartDate()) ?>
                            <br/>
                            по <?php echo D::toLongRus($activity['activity']->getEndDate()) ?>
    <?php endif; ?>
            </td>
            <td class="complete dealers-list-handler" data-url="<?php echo url_for('@agreement_module_activity_dealers_done?id='.$id.'&year='.$year) ?>"><?php echo count($activity['done_dealers']), ' ', RusUtils::pluralDealerEnding(count($activity['done_dealers'])) ?></td>
            <td class="progress dealers-list-handler" data-url="<?php echo url_for('@agreement_module_activity_dealers_in_work?id='.$id.'&year='.$year) ?>"><?php echo count($activity['in_work_dealers']), ' ', RusUtils::pluralDealerEnding(count($activity['in_work_dealers'])) ?></td>
            <td class="blank dealers-list-handler" data-url="<?php echo url_for('@agreement_module_activity_dealers_no_work?id='.$id.'&year='.$year) ?>"><?php echo count($activity['no_work_dealers']), ' ', RusUtils::pluralDealerEnding(count($activity['no_work_dealers'])) ?></td>
        </tr>
  <?php endforeach; ?>
    </table>

<?php use_javascript('dealers/list_popup') ?>
<script type="text/javascript">
$(function() {
    new DealersListPopup({
        handler_selector: '#activities_table .dealers-list-handler',
        popup_selector: '#dealer-list'
    }).start();

    $('.modal-select-dropdown-item').on('click', function () {
        window.location.href = $(this).data('url');
    });
});
</script>

<?php endif; ?>

<?php if(!$is_finished): ?>
<div><a href="<?php echo url_for('@agreement_module_finished_activities?year='.$year) ?>" class="lnk-button lnk-button-o">Посмотреть завершенные</a></div>
<?php endif; ?>

</div>