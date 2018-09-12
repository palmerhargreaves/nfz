<div class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="<?php echo url_for('home/index') ?>">Servicepool NFZ</a>
        <ul class="nav">
            <?php if ($sf_user->hasCredential('admin')): ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Пользователи
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php include_partial('global/navigation_item', array('name' => 'Группы', 'module' => 'user_group', 'role' => 'admin')); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Пользователи', 'module' => 'user', 'role' => 'admin')); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Привязанные дилеры', 'module' => 'dealer_user', 'role' => 'admin')); ?>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if ($sf_user->hasCredential('importer') && !$sf_user->hasCredential('admin')): ?>
                <?php include_partial('global/navigation_item', array('name' => 'Менеджеры', 'module' => 'user', 'role' => 'importer')); ?>
            <?php endif; ?>
            <?php if ($sf_user->hasCredential('admin')): ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Материалы
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php include_partial('global/navigation_item', array('name' => 'Категории', 'module' => 'material_category', 'role' => 'admin')); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Материалы', 'module' => 'material', 'role' => 'admin')); ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ($sf_user->hasCredential('importer')): ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Активности
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php include_partial('global/navigation_item', array('name' => 'Активности', 'module' => 'activity', 'role' => 'importer')); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статусы активностей', 'module' => 'activities_status', 'role' => 'importer')); ?>
                    </ul>
                </li>
            <?php endif; ?>

            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    Дилеры
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <?php include_partial('global/navigation_item', array('name' => 'Бюджеты дилеров', 'module' => 'dealer', 'role' => 'importer')); ?>
                    <?php include_partial('global/navigation_item', array('name' => 'Список дилеров', 'module' => 'dealer_list', 'role' => 'importer')); ?>
                    <?php include_partial('global/navigation_item', array('name' => 'Загрузить бюджет', 'module' => 'dealers_budgets_files', 'role' => 'admin')); ?>
                </ul>
            </li>

            <?php
            if ($sf_user->hasCredential(array('admin'), false))
                include_partial('global/navigation_item', array('name' => 'Новости', 'module' => 'news', 'role' => 'admin'));
            include_partial('global/navigation_item', array('name' => 'Faqs', 'module' => 'faqs', 'role' => 'admin'));

            include_partial('global/navigation_item', array('name' => 'Календарь', 'module' => 'calendar', 'role' => array('admin', 'manager', 'importer')));
            ?>

            <?php if ($sf_user->hasCredential(array('manager', 'impoter', 'admin'), false)): ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Дополнительно
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php include_partial('global/navigation_item', array('name' => 'Перенос заявок', 'module' => 'models_dates', 'role' => array('admin'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Диалоги', 'module' => 'dialogs', 'role' => array('admin'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Бюджеты', 'module' => 'dealer_work_statistic', 'role' => array('admin'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Сервисные акции', 'module' => 'dealer_services_dialogs', 'role' => array('admin'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Расширенная статистика', 'module' => 'activity_extended_statistic', 'role' => array('admin'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Удаленные заявки', 'module' => 'deleted_models', 'role' => array('admin'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Параметры активностей', 'module' => 'activity_statistic_settings', 'role' => array('admin'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Заблокированные заявки', 'module' => 'agreement_models_blocked_statistics', 'role' => array('admin'))); ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ($sf_user->hasCredential(array('manager', 'impoter', 'admin'), false)): ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Статистика
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по количеству прокомментированных макетов', 'module' => 'comment_stat', 'role' => array('manager', 'importer'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Выгрузка отчетов', 'module' => 'reports_info', 'role' => array('admin', 'manager', 'importer'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Выгрузка макетов', 'module' => 'makets_info', 'role' => array('admin', 'manager', 'importer'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по активностям', 'module' => 'activity_stats', 'role' => array('admin', 'manager', 'importer'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по загрузкам', 'module' => 'gazeta', 'role' => array('admin', 'manager', 'importer'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по скачиванию материалов', 'module' => 'material', 'action' => 'downloads', 'role' => array('admin', 'manager', 'importer'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по активностям (дилеры)', 'module' => 'activities_stats', 'role' => array('admin', 'manager', 'importer'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по email (дилеры)', 'module' => 'mailing_list', 'role' => array('admin', 'manager', 'importer'))); ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ($sf_user->hasCredential(array('manager', 'impoter', 'admin'), false)): ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        Акции
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по акции', 'module' => 'special_budget_stat', 'role' => array('admin', 'manager', 'impoter'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по Сервисной акции', 'module' => 'summer_service_action', 'role' => array('admin', 'manager', 'impoter'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по осенней акции', 'module' => 'summer_service_action2', 'role' => array('admin', 'manager', 'impoter'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по Велосипедной акции', 'module' => 'bikes', 'role' => array('admin', 'manager', 'impoter'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по Велосипедной акции 2', 'module' => 'bikes2', 'role' => array('admin', 'manager', 'impoter'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по Продукты года (3 этап)', 'module' => 'prod_of_year3', 'role' => array('admin', 'manager', 'impoter'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по Зимней акции', 'module' => 'winter_service_action', 'role' => array('admin', 'manager', 'impoter'))); ?>
                        <?php include_partial('global/navigation_item', array('name' => 'Статистика по Сервисным Акциям', 'module' => 'service_actions', 'role' => array('admin', 'manager', 'impoter'))); ?>
                    </ul>
                </li>
            <?php endif; ?>

        </ul>
        <ul class="nav pull-right">
            <li><a href="<?php echo url_for('auth/logout') ?>"><i class="icon-off"></i> Выйти</a></li>
        </ul>
    </div>
</div>
