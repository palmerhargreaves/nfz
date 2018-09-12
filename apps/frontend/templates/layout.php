<!DOCTYPE html>
<html>
<head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>

    <?php if ($sf_user->isDealerUser() || $sf_user->isManager()): ?>
        <script type="text/javascript">
            $(function () {
                window.common_discussion = new DealerDiscussion({
                    panel: '#chat-modal',
                    state_url: "<?php echo url_for('@discussion_state') ?>",
                    new_messages_url: "<?php echo url_for('@discussion_new_messages') ?>",
                    post_url: "<?php echo url_for('@discussion_post') ?>",
                    dealer_discussion_url: "<?php echo url_for('@get_dealer_discussion') ?>",
                    previous_url: "<?php echo url_for('@discussion_previous') ?>",
                    search_url: "<?php echo url_for('@discussion_search') ?>",
                    online_check_url: "<?php echo url_for('@discussion_online_check') ?>",
                    uploader: new Uploader({
                        selector: '#chat-modal .message-upload',
                        session_name: '<?php echo session_name() ?>',
                        session_id: '<?php echo session_id() ?>',
                        upload_url: '/upload.php',
                        delete_url: "<?php echo url_for('@upload_temp_delete') ?>"
                    }).start()
                }).start();

                window.discussion_comments_form = window.common_discussion;

                window.service_clinic_stats = new ServiceClinicStats({
                    modal: '#service-clinic-stats-modal',
                    show_url: '<?php echo url_for('@service_clinic_stats_show'); ?>'
                }).start();
            });
        </script>

        <script>
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-5542235-15', 'auto');
            ga('send', 'pageview');

        </script>
    <?php endif; ?>

    <!--[if lte IE 8]>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/ie.css"/>
    <![endif]-->

</head>
<body class="authorized"
      data-max-upload-file-size="<?php echo sfConfig::get('app_max_upload_size'); ?>"
      data-max-files-upload-count="<?php echo sfConfig::get('app_max_files_upload_count'); ?>"
      data-allow-model-files-types-scenario="<?php echo sfConfig::get('app_allow_files_types_model_scenario'); ?>"
      data-allow-model-files-types-record="<?php echo sfConfig::get('app_allow_files_types_model_record'); ?>">
<div id="swfupload"></div>

<div class="alert-popup fancybox-margin" id="j-alert-global" style="display: none;">
    <div class="alert-popup__content">
        <div class="alert j-wrap alert-error">
            <button type="button" class="close"><i class="fa fa-times"></i></button>
            <div class="alert-title j-title"></div>
            <p class="alert-message j-message"></p>
        </div>
    </div>
</div>

<?php if ($sf_user->isDealerUser() || $sf_user->isManager()): ?>
    <div id="chat-modal" style="width:640px;"
         class="chat wide modal"<?php if ($sf_user->isDealerUser() && $sf_user->getAuthUser()->getDealerDiscussion()) echo ' data-dealer-discussion="' . $sf_user->getAuthUser()->getDealerDiscussion()->getId() . '"'; ?><?php if ($sf_user->isManager()) echo ' data-manager-discussion="yes"'; ?>>
        <div class="white modal-header">Задать вопрос
            <?php include_partial('discussion/form_search'); ?>
        </div>
        <div class="modal-close"></div>
        <?php if ($sf_user->isManager()): ?>
            <select name="dealer" style="margin-left: 16px;">
                <option value="">-- выберите дилера --</option>
                <?php foreach (DealerTable::getVwDealersQuery()->execute() as $dealer): ?>
                    <option value="<?php echo $dealer->getId() ?>"><?php echo $dealer ?></option>
                <?php endforeach; ?>

            </select>
        <?php endif; ?>
        <?php include_partial('discussion/panel') ?>
    </div>
<?php endif;

if ($sf_user->isManager() || $sf_user->isImporter()):
    ?>
    <div id="service-clinic-stats-modal" class="chat wide modal" style="width:480px;">
        <div class="white modal-header">Статистика Service Clinic

        </div>
        <div class="modal-close"></div>

        <div class="modal-service-clinic-stats-content-container"></div>
        <?php //include_partial('activity/')
        ?>
    </div>
<?php endif;

if ($sf_user->getAuthUser()->isSuperAdmin()) {
    ?>
    <div id="special-modal" class="chat wide modal" style="width:640px;">
        <div class="white modal-header">Комментарии
            <?php include_partial('discussion/form_search'); ?>
        </div>
        <div class="modal-close"></div>

        <?php include_partial('discussion/special_panel') ?>
    </div>

<?php } ?>

<?php include_partial('global/modal_confirm_delete') ?>
<div id="site">
    <div id="header">
        <a href="<?php echo url_for('@homepage') ?>" class="logo"></a>
        <a href="<?php echo url_for('@homepage') ?>" class="header"></a>
        <div id="menu-wrapper">
            <?php include_partial('global/menu_service') ?>
            <?php //include_component('history', 'unread') ?>

            <a href="<?php echo url_for('@homepage') . "main" ?>">
                <div id="user-messages">
                    <div class="num-bg"></div>
                    <div class="num" style="font-size: 10px; font-weight: normal; color: #9999a3; ">Вернуться на
                        главную
                    </div>
                </div>
            </a>

        </div>

        <div class="nav-header">
            <ul>
                <?php if ($sf_user->isDealerUser()):
                    $userDealer = $sf_user->getAuthUser()->getDealerUsers()->getFirst();
                    if ($userDealer):
                        ?>
                        <li>
                            <a href="http://nfz.palmer-hargreaves.ru/index.php?r=turnover%2Findex&sp_dealer_id=<?php echo $userDealer->getDealerId(); ?>">Оборот
                                NFZ</a></li>
                    <?php endif; ?>
                <?php elseif ($sf_user->isImporter()): ?>
                    <li><a href="http://nfz.palmer-hargreaves.ru/index.php?r=turnover%2Findex&importer=true">Оборот
                            NFZ</a></li>
                <?php elseif ($sf_user->isRegionalManager()): ?>
                    <li><a href="http://nfz.palmer-hargreaves.ru/index.php?r=turnover%2Findex&importer=true&old_sp_id=<?php echo $sf_user->getAuthUser()->getNaturalPersonId(); ?>">Оборот
                            NFZ</a></li>
                <?php endif; ?>

                <li><a href="<?php echo url_for('news') ?>">Новости</a></li>
                <li><a href="<?php echo url_for('faqs') ?>">FAQ</a></li>

                <?php
                $dealer_user = $sf_user->getAuthUser()->getDealerUsers()->getFirst();
                if ($sf_user->isDealerUser() && $dealer_user):
                    ?>
                    <li><a href="<?php echo url_for('@agreement_module_model_activities') ?>">Мои заявки</a></li>
                <?php endif; ?>

                <?php if (getenv('REMOTE_ADDR') == '46.175.166.61'): ?>
                    <a href="javascript:;" id="begin-chat">
                        <div
                                class="ico<?php if (DealerDiscussionTable::getInstance()->countUnread($sf_user->getRawValue()->getAuthUser()) > 0) echo ' new' ?>"></div>
                        Задать вопрос
                    </a>
                <?php endif; ?>

                <?php if ($sf_user->isDealerUser() || $sf_user->isManager()): ?>
                    <li>
                        <?php if ($sf_user->getAuthUser()->isSuperAdmin()) { ?>
                            <a href="<?php echo url_for('@discussion_messages'); ?>">
                                Сообщения
                            </a>
                        <?php } else { ?>
                            <a href="javascript:;" id="begin-chat">
                                <div
                                    class="ico<?php if (DealerDiscussionTable::getInstance()->countUnread($sf_user->getRawValue()->getAuthUser()) > 0) echo ' new' ?>"></div>
                                Задать вопрос
                            </a>
                        <?php } ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <?php /*
    <?php
      if($sf_user->getAuthUser()->isSuperAdmin()) {
    		$newM = DealerDiscussionTable::getInstance()->countUnread($sf_user->getRawValue()->getAuthUser());
    ?>
      <a href="<?php echo url_for('@discussion_all_messages'); ?>">
		    <div id="special-button" style="<?php echo $newM > 0 ? 'width: 145px;' : ''; ?> ">
        	<div class="ico<?php if($newM > 0) echo ' new' ?>"></div>
        	<?php echo $newM > 0 ? "Есть новые сообщения" : "Сообщения" ; ?>
      	</div>
      </a>
    <?php
    	} else {
    ?>
    <?php if($sf_user->isDealerUser() || $sf_user->isManager()): ?>
			<div id="chat-button">
          		<div class="ico<?php if(DealerDiscussionTable::getInstance()->countUnread($sf_user->getRawValue()->getAuthUser()) > 0) echo ' new' ?>"></div>
             		Задать вопрос
      		</div>
    <?php endif; 
    	}
    ?>
*/ ?>
    </div>

    <div id="content">
        <?php echo $sf_content ?>
    </div>

    <?php include_partial('global/footer') ?>

</div>

<div id='post-bg'
     style='position: absolute; display: none; width: 100%; height: 100%; top: 0px; left: 0px; background: rgba(128, 128, 128, 0.38); z-index: 1000;'></div>

<iframe style="position: absolute;" src="/blank.html" width="1" height="1" frameborder="0" hspace="0" marginheight="0"
        marginwidth="0" name="agreement-model-comments-frame" scrolling="no"></iframe>

</body>
</html>
