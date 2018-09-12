<?php decorate_with('layout_links') ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <title>Volkswagen Service</title>
    <link rel="icon" type="image/png" href="favicon.ico"/>
    <link media="screen" href="css/fonts.css" type="text/css" rel="stylesheet"/>
    <link media="screen" href="css/style.css" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/libs.js"></script>
</head>
<body>
<div id="wrap">
    <div id="layout">
        <div id="header">
            <div class="logo">
                <img src="images/logo_main.png" alt="Volkswagen Service">
            </div>
        </div>
        <div id="main">
            <div class="menu">
                <div class="item">
                    <a target="_blank" href="http://vw-servicepool.ru/">
                        <i><img src="images/menu/01.jpg" alt=""></i>
                        <span><em>vw-servicepool.ru</em></span>
                    </a>
                </div>

                <div class="item">
                    <a target="_blank" href="http://nfz.vw-servicepool.ru/">
                        <i><img src="images/menu/03.jpg" alt=""></i>
                        <span><em>Согласование материалов NFZ</em></span>
                    </a>
                </div>

                <?php
                $dealer = $sf_user->getAuthUser()->getDealer();

                $canNfzAccess = false;
                if ($sf_user->getAuthUser()->isAdmin() || $sf_user->getAuthUser()->isDesigner()) {
                    $canNfzAccess = true;
                } else if ($dealer && $dealer->isNFZ_PKW()) {
                    $canNfzAccess = true;
                }

                if ($canNfzAccess):
                    ?>
                    <div class="item">
                        <a target="_blank" href="http://dm.vw-servicepool.ru/">
                            <i><img src="images/menu/02.jpg" alt=""></i>
                            <span><em>Согласование материалов PKW</em></span>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="item">
                    <a target="_blank" href="http://redactor.vw-servicepool.ru/">
                        <i><img src="images/menu/04.jpg" alt=""></i>
                        <span><em>Редактор макетов</em></span>
                    </a>
                </div>
                <div class="item">
                    <a target="_blank" href="http://vw-service-offers.ru/dealers">
                        <i><img src="images/menu/05.jpg" alt=""></i>
                        <span><em>Service Offensive online</em></span>
                    </a>
                </div>

                <?php
                $user = $sf_user->getAuthUser();
                $encode = $user->getEncodedToken();

                if ($encode): ?>
                    <div class="item">
                        <a target="_blank"
                           href="http://survey.vw-servicepool.ru/?oitokenauth=<?php echo urlencode($encode); ?>">
                            <i><img src="images/menu/06.jpg" alt=""></i>
                            <span><em>Опросы</em></span>
                        </a>
                    </div>
                <?php endif; ?>
                <div class="item">
                    <a target="_blank"
                       href="http://vw-training.ru/?oitokenauth=<?= md5($user->getEmail() . $user->getId()); ?>">
                        <i><img src="images/menu/07.jpg" alt=""></i>
                        <span><em>Training Portal</em></span>
                    </a>
                </div>

                <div class="item">
                    <a target="_blank" href="//competition.vw-servicepool.ru/site/auth-from-nfz-token/?token=<?php echo $user->getPassword(); ?>&email=<?php echo $user->getEmail(); ?>">
                        <i><img src="images/menu/otbutton.jpg" alt=""></i>
                        <span><em>Прибавь обороты!</em></span>
                    </a>
                </div>

                <div style="clear:both"></div>
            </div>
        </div>
        <div id="footer">
            <div class="copyright">
                &copy;<a href="" target="_blank"> Volkswagen</a> |
                <a href="http://www.volkswagen.ru/" target="_blank">Фольксваген Россия</a> |
                <a href="http://www.volkswagen.ru/ru/tools/navigation/footer/rights.html" target="_blank">Правовые
                    аспекты</a>
            </div>
            <div class="company">
                <a href="http://www.volkswagenag.com/content/vwcorp/content/de/homepage.html" target="_blank">Volkswagen
                    AG</a> |
                <a href="http://www.volkswagen.com/" target="_blank">Volkswagen International</a>
            </div>
        </div>


    </div>
</div>
</body>
</html>
