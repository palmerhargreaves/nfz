<?php
$img = $info->getImgBig();
if (empty($img)) {
    $img = $info->getImgSmall();
}

?>

<div class="d-table">
    <?php if (!empty($img)) { ?>
        <div class="d-cell cell-img">
            <a href="#article-details-<?php echo $info->getId(); ?>" class="js-show-popup-article"><i><img
                        src="/uploads/news/images/<?php echo $img; ?>" alt="<?php echo $info->getName(); ?>"/></i></a>
        </div>
    <?php } ?>

    <?php
        $text = $raw_text = $info->getRawValue()->getText();
        $has_split_text_tag = strpos($text, '[split_text]');
        $split_to = sfConfig::get('app_news_split_to');

        $text = str_replace('[split_text]', '', $text);
        if ($has_split_text_tag !== FALSE) {
            $split_to = $has_split_text_tag;
        }

        //array_reverse();
    ?>


    <div class="d-cell cell-descr">
        <div class="article-date"><?php echo $info->getCreatedAt(); ?></div>
        <div class="article-title" title="<?php echo $info->getName(); ?>"><a
                href="#article-details-<?php echo $info->getId(); ?>"
                class="js-show-popup-article"><strong><?php echo $info->getName(); ?></strong></a></div>
        <div class="article-preview" title="<?php //echo $info->getRawValue()->getAnnouncement(); ?>">
            <?php echo Utils::trim_text($text, $split_to, false, false); ?>
        </div>


        <?php if (mb_strlen(strip_tags($raw_text)) > $split_to && is_null($read_next)): ?>
            <div class="article-read"><a href="#article-details-<?php echo $info->getId(); ?>"
                                         class="js-show-popup-article">Читать далее</a></div>
        <?php endif; ?>
    </div>
    <div class="d-cell cell-button">
        <a href="#article-details-<?php echo $info->getId(); ?>" class="lnk-button lnk-button-o js-show-popup-article">Подробнее</a>
    </div>
</div>

<div style="display:none">
    <div class="article-details wide modal" id="article-details-<?php echo $info->getId(); ?>">
        <div class="modal-close"></div>
        <div class="article-details-i">
            <p><img src="/uploads/news/images/<?php echo $img; ?>" alt="<?php echo $info->getName(); ?>"></p>
            <h2><?php echo $info->getName(); ?></h2>
            <?php echo $text; ?>
        </div>
    </div>
</div>
