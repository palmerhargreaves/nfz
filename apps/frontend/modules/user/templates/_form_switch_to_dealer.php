<div id="switch-to-dealer" class="modal">
    <div class="modal-header">Переключиться на дилера</div>
    <div class="modal-close"></div>
    <form action="<?php echo url_for('user/switchToDealer') ?>" method="post" id="switch-to-dealer-form">
        <div class="modal-input-label">&nbsp;</div>
        <div class="modal-select-wrapper select krik-select">
            <span class="select-value">Выберите дилерское предприятие</span>
            <span class="select-filter"><input type="text"></span>
            <input type="hidden" name="dealer_id">
            <div class="ico"></div>
            <div class="modal-input-error-icon error-icon"></div>
            <div class="modal-select-dropdown">
                <?php
                if ($sf_user->getAuthUser()->isRegionalManager()) {
                    $userDealers = $sf_user->getAuthUser()->hasDealersListFromNaturalPerson();

                    $tmp = array();
                    foreach ($userDealers as $k => $i) {
                        $tmp[] = $k;
                    }

                    $userDealers = DealerTable::getVwDealersQuery()->whereIn('d.id', $tmp)->execute();
                } else {
                    $userDealers = $sf_user->getAuthUser()->getDealersList();

                    if (empty($userDealers))
                        $userDealers = DealerTable::getVwDealersQuery()->execute();
                    else {
                        $tmp = array();
                        foreach ($userDealers as $k => $i)
                            $tmp[] = $k;

                        $userDealers = DealerTable::getVwDealersQuery()->whereIn('d.id', $tmp)->execute();
                    }
                }


                foreach ($userDealers as $dealer): ?>
                    <div class="modal-select-dropdown-item select-item"
                         data-value="<?php echo $dealer->getId() ?>"><?php echo $dealer ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="modal-button-wrapper"><input id="change-button" type="submit" class="modal-button button"
                                                 value="Переключиться"></div>
    </form>
</div>