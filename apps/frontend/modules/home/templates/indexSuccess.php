<?php $bStr = 'Бюджет на ' . $year . ' г.'; ?>

<?php if ($sf_user->getAuthUser()->isDealerUser() && $sf_user->getAuthUser()->getDealer()): ?>
    <?php include_component('budget_by_points', 'budgetPanel', array(
        'dealer' => $sf_user->getAuthUser()->getDealer(),
        'header' => $bStr,
        'year' => $year,
        'budYears' => $budgetYears
    )); ?>
    <?php include_component('mailing', 'mailingPanel'); ?>
<?php endif; ?>

<div class="clear"></div>
<div class="actions-wrapper">
    <?php include_component('news', 'lastNews'); ?>

    <div class="activity-main-page">
        <!-- Nav tabs -->
        <div class="tabs-activity">
            <ul class="nav nav-tabs">
                <li class="active"><a href="javascript:;" name="notFinished" class="tabHeader">Текущие активности</a>
                </li>
                <li><a href="javascript:;" name="finished" class="tabHeader">Завершённые активности</a></li>
                <?php if ($sf_user->getAuthUser()->isDealerUser()): ?>
                    <li><a href="javascript:;" name="activities" class="tabHeader">Статистика</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="notFinished">
                <?php include_component('activity', 'notFinishedActivities', array('onlyShow' => $onlyShow)); ?>
            </div>
            <div class="tab-pane" id="finished">
                <?php include_component('activity', 'finished', array('onlyShow' => $onlyShow)); ?>
            </div>
            <?php if ($sf_user->getAuthUser()->isDealerUser()): ?>
                <div class="tab-pane" id="activities">
                    <?php include_component('activity', 'dealerStatistics', array('onlyShow' => $onlyShow)); ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
    <?php

    ?>
</div>

<a>&nbsp;</a>

<?php

include_partial('intro_modal');
$userCertificate = false;

if ($sf_user->getRawValue()->getAuthUser()->getIsFirstLogin()) {

    $infoDialog = DialogsTable::getLastActiveInfoDialog(true);

    $sf_user->getRawValue()->getAuthUser()->setIsFirstLogin(false);
    $sf_user->getRawValue()->getAuthUser()->save();

    include_partial('info_modal', array('data' => $infoDialog));

} else if (($infoDialog = DialogsTable::getBindedDialog($sf_user->getRawValue()->getAuthUser())) != null) {
    include_partial('info_modal', array('data' => $infoDialog));
} else {
    $infoDialog = DialogsTable::getLastActiveInfoDialog();
    $serviceActive = DealerServicesDialogsTable::isActiveForUser($sf_user->getRawValue()->getAuthUser());

    if (!empty($serviceActive) && count($serviceActive) > 0) {
        if (count($serviceActive) > 1) {
            include_partial('service_action_modal_choose', array('data' => $serviceActive));

            include_partial('service_action_modal', array('data' => $serviceActive[0], 'cls' => 'service-action-modal-container'));
            include_partial('service_action_modal_success', array('data' => null, 'cls' => 'service-action-modal-choose-success'));
        } else {
            include_partial('service_action_modal', array('data' => $serviceActive[0], 'cls' => null));
            include_partial('service_action_modal_success', array('data' => $serviceActive[0]));
        }
    } else if ($infoDialog) {
        include_partial('info_modal', array('data' => $infoDialog));
    } else if (!$sf_user->getAuthUser()->checkForFillExtendedStatistic()) {
        $userCertificate = false;
        include_partial('msg_modal');
    }
}

if (!$sf_user->getRawValue()->getAuthUser()->isPostSelected()):
    include_partial('users_post_modal');
endif;
?>

<?php if ($userCertificate): ?>
    <script>
        $(function () {
            if (RegExp('msg', 'gi').test(window.location.search)) {
                $("#msg-modal").krikmodal('show');
            }
        });
    </script>
<?php endif; ?>


<?php if ($infoDialog): ?>
    <script>
        $(function () {
            if (RegExp('info', 'gi').test(window.location.search)) {
                $("#info-modal").krikmodal('show');
            }
        });
    </script>
<?php endif; ?>

<?php if (count($serviceActive) > 1): ?>
    <script>
        $(function () {
            if (RegExp('service', 'gi').test(window.location.search)) {
                $("#service-action-choose-modal").krikmodal('show');
            }
        });
    </script>
<?php else: ?>
    <script>
        $(function () {
            if (RegExp('service', 'gi').test(window.location.search)) {
                $("#service-action-modal").krikmodal('show');
            }
        });
    </script>
<?php endif; ?>

<?php if (!$sf_user->getRawValue()->getAuthUser()->isPostSelected()): ?>
    <script>
        $(function () {
            $('#post-bg').css('height', $(document).height());
            $('#post-bg').show();

            $("#users-post-modal").krikmodal('show');
        });
    </script>
<?php endif; ?>

