<div class="inner-page-title"><h1><a
            href="<?php echo url_for('@agreement_module_activities') ?>">Активности</a> <?php echo $builder->getActivity()->getName() ?>
    </h1></div>

<?php include_partial('agreement_activity_model_management/modal_model', array('decline_reasons' => $decline_reasons, 'decline_report_reasons' => $decline_report_reasons, 'specialist_groups' => $specialist_groups)) ?>
<?php include_partial('agreement_activity_model_management/menu', array('active' => 'activities', 'year' => $year, 'url' => 'agreement_module_activities')) ?>

<?php
$statsResult = $builder->getStat();

$stats = $statsResult['dealers'];
$tempStats = $statsResult['extended'];

$extendedStats = array(1 => '', 2 => '', 3 => '', 4 => '');
foreach ($extendedStats as $qKey => $data) {
    if (isset($tempStats[$qKey])) {
        $extendedStats[$qKey] = $tempStats[$qKey];
    } else {
        unset($extendedStats[$qKey]);
    }
}
?>

<div id="filters" class="inline">
    <form id="frmActivityDealers" name="frmActivityDealers"
          action="<?php echo url_for('@agreement_module_activity?id=' . $builder->getActivity()->getId()); ?>"
          data-url="<?php echo url_for('@agreement_module_activity_export') ?>" method="post">
        <div class="d-legend"><span>Параметры экспорта</span></div>

        <input type="button" id="bt-make-export-data" value="Экспорт" class="lnk-button d-fr"/>

        <div class="modal-select-wrapper krik-select select dealer filter">
            <?php if ($dealer_filter): ?>
                <span class="select-value"><?php echo $dealer_filter->getRawValue(); ?></span>
                <input type="hidden" name="dealer" value="<?php echo $dealer_filter->getId(); ?>">
            <?php else: ?>
                <span class="select-value">Все дилеры</span>
                <input type="hidden" name="dealer">
            <?php endif; ?>

            <div class="ico"></div>
            <span class="select-filter" style="display: none;"><input type="text"></span>
            <div class="modal-input-error-icon error-icon"></div>
            <div class="error message"></div>
            <div class="modal-select-dropdown" style="display: none;">
                <div class="modal-select-dropdown-item select-item" data-value="">Все</div>
                <?php foreach (DealerTable::getVwDealersQuery()->execute() as $dealer): ?>
                    <div class="modal-select-dropdown-item select-item"
                         data-value="<?php echo $dealer->getId() ?>"><?php echo sprintf('[%s] %s', $dealer->getShortNumber(), $dealer->getName()); ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="date-input filter">
            <input type="text" placeholder="от" name="start_date"
                   value="<?php echo $start_date_filter ? date('d.m.Y', $start_date_filter) : '' ?>"
                   class="with-date"/>
        </div>

        <div class="date-input filter">
            <input type="text" placeholder="до" name="end_date" class="with-date"
                   value="<?php echo $end_date_filter ? date('d.m.Y', $end_date_filter) : '' ?>"/>
        </div>

        <input type="hidden" name="activity_id" value="<?php echo $activityId; ?>"/>
        <input type="hidden" name="view_data"
               value="<?php echo $view_data_filter ? ($view_data_filter == 'all' ? 'quarters' : 'all') : 'quarters'; ?>"/>
    </form>
</div>

<div class="d-legend"><span>Дилеры</span></div>
<div class="activities tabbed-wrap" id="agreement-models">
    <div id="materials" class="active">
        <?php if (count($extendedStats) == 0) { ?>
            Ничего не найдено!
        <?php } /* else if ($view_data_filter == "all") {
            include_partial('activity_dealers_quarters', array('extendedStats' => $extendedStats));
        }*/ else {
            include_partial('activity_dealers_quarters', array('extendedStats' => $extendedStats));
        }
        ?>
    </div><!-- /#materials -->

</div>

<script>
    $(function () {
        $('.filter .with-date').datepicker();

        $('#filters form :input[name]').change(function () {
            this.form.submit();
        });

        $('#ch-show-all-data').change(function () {
            $('#frmActivityDealers').submit();
        });

        $('#bt-make-export-data').live('click', function () {
            var $bt = $(this),
                $dealer = $("input[name=dealer]"),
                $modelWorkStatus = $("input[name=model_work_status]"),
                $activityId = $("input[name=activity_id]"),
                $form = $dealer.closest('form');

            $bt.fadeOut();
            $.post($form.data('url'),
                {
                    dealer: $dealer.val(),
                    model_work_status: $modelWorkStatus.val(),
                    activity_id: $activityId.val()
                },
                function (result) {
                    $bt.fadeIn();

                    if (result.success) {
                        location.href = result.url;
                    }
                }
            );
        });

        $('.tab-pane').each(function(ind, el) {
            if ($(el).hasClass('active')) {
                $(el).show();
            }
        });

        $('.tbl-tr-toggle').click(function() {
            $('.tbl-tr-toggled[data-toggle=' + $(this).data('toggle') + ']', $(this).parent()).toggle();

            $(this).find('td:last > i').toggleClass('minus');
        });

        $('.tabHeader').click(function() {
            $('.tabHeader').removeClass('active');
            $(this).addClass('active');

            $('.tab-pane').hide();
            $('#activities-q-' + $(this).data('q-idx')).show();
        });
    });
</script>