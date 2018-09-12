<div id="user-menu">
    <div class="name-wrapper">
        <?php include_component('user', 'user') ?>
    </div>
    <ul class="items">
        <li class="item" id="pass-change-link">Смена пароля</li>
        <?php
        $dealer_user = $sf_user->getAuthUser()->getDealerUsers()->getFirst();
        if ($dealer_user && $dealer_user->getManager()):
            ?>
            <li class="item"><a href="<?php echo url_for('dealer_user/index') ?>">Управление пользователями</a></li>
        <?php endif; ?>

        <?php if ($sf_user->isImporter()): ?>
            <li class="item"><a href="<?php echo url_for('@agreement_module_activities_status') ?>">Выполнение
                    активностей</a></li>
            <li class="item"><a href="<?php echo url_for('@agreement_module_specialist_users') ?>">Пользователи</a></li>
            <li class="item"><a href="<?php echo url_for('@agreement_module_specialist_search') ?>">Поиск заявок</a>
            </li>
            <li class="item"><a href="<?php echo url_for('@mailing_stat') ?>">Статистика по Email</a></li>
        <?php endif; ?>
        <?php if ($sf_user->isSpecialist()): ?>
            <li class="item"><a
                    href="<?php echo url_for('@agreement_module_specialist_models') ?>">Согласование<?php if ($sf_user->isManager()) echo ' (специалист)' ?></a>
            </li>
        <?php endif; ?>

        <?php if ($sf_user->isRegionalManager()): ?>
            <li class="item"><a href="<?php echo url_for('@agreement_module_activities_status') ?>">Выполнение
                    активностей</a></li>
        <?php endif; ?>

        <?php if ($sf_user->isManager() || $sf_user->isImporter()): ?>
            <li class="item"><a
                    href="<?php echo url_for('@agreement_module_management_models') ?>">Согласование<?php if ($sf_user->isSpecialist() || $sf_user->isImporter()) echo ' (менеджер)' ?></a>
            </li>
        <?php endif; ?>

        <?php if ($sf_user->isAdmin() || $sf_user->isManager() || $sf_user->isSpecialist()): ?>
            <li class="item"><a href="<?php echo url_for('favorites_reports') ?>">Отложенные отчеты</a></li>
        <?php endif; ?>

        <?php if ($sf_user->isManager() || $sf_user->isImporter()): ?>
            <li class="item" id="switch-to-dealer-link">Переключиться на дилера</li>
            <!--<li class="item"><a href="<?php //echo url_for('@activity_statistic_info') ?>">Статистика по активностям</a></li>-->
            <?php if ($sf_user->isDealerUser()): ?>
                <li class="item"><a href="<?php echo url_for('user/detachFromDealer') ?>">Отключиться от дилера</a></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php

        if ($sf_user->isRegionalManager()) {
            $dealers = $sf_user->getAuthUser()->hasDealersListFromNaturalPerson();
        } else {
            $dealers = $sf_user->getAuthUser()->getDealersList();
        }
        ?>

        <?php if ($dealers && count($dealers) > 0): ?>
            <li class="item" id="switch-to-dealer-link">Переключиться на дилера</li>

            <?php if ($sf_user->isDealerUser() && $sf_user->isRegionalManager()): ?>
                <li class="item"><a href="<?php echo url_for('user/detachFromDealer') ?>">Отключиться от дилера</a></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($sf_user->isManager()): ?>
            <!--<li class="item" id="show-service-clinic-stats">Статистика Service Clinic</li>-->
        <?php endif; ?>

        <?php if (($sf_user->getAuthUser()->isAdmin() || $sf_user->getAuthUser()->isDesigner()) && !$sf_user->getAuthUser()->isImporterByGroup()): ?>
            <li class="item"><a href="<?php echo sfConfig::get('app_site_url'); ?>/history">История заявок</a></li>
        <?php endif; ?>

        <?php if ($sf_user->isDealerUser()): ?>
            <!--<li id="intro-button" class="item"><a href="<?php echo url_for('@homepage') ?>?start-tour=yes">Демонстрационный тур</a></li>-->
        <?php endif; ?>
        <li class="item last"><a href="<?php echo url_for('auth/logout') ?>">Выход</a></li>
    </ul>
</div>

<?php include_partial('user/form_change_password') ?>

<?php if ($sf_user->isManager() || $sf_user->isImporter() || $sf_user->isDealerUser() || $sf_user->isRegionalManager()): ?>
    <?php include_partial('user/form_switch_to_dealer') ?>
<?php endif; ?>
