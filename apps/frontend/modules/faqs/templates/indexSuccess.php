<div class="articles-wrap">

	<h1>FAQ</h1>

	<div class="list-articles d-cb">
	<?php
		foreach($faqs as $info):
			$img = $info->getImage();
	?>
		<div class="list-article">
			<div class="d-table">
			<?php if(!empty($img)){?>
				<div class="d-cell cell-img">
					<a href="#article-details-<?php echo $info->getId();?>" class="js-show-popup-article"><i><img src="/uploads/news/images/<?php echo $img; ?>" alt="<?php echo $info->getQuestion(); ?>"></i></a>
				</div>
			<?php }?>

                <div class="d-cell cell-descr">
					<div class="article-date"><?php echo $info->getCreatedAt(); ?></div>
					<div class="article-title" title="<?php echo $info->getQuestion(); ?>"><a href="#article-details-<?php echo $info->getId();?>" class="js-show-popup-article"><strong><?php echo $info->getQuestion(); ?></strong></a></div>
					<div class="article-preview" title="<?php echo Utils::trim_text($info->getAnswer(), 150); ?>"><?php echo Utils::trim_text($info->getAnswer(), 150); ?></div>
				</div>
				<div class="d-cell cell-button">
					<a href="#article-details-<?php echo $info->getId();?>" class="lnk-button lnk-button-o js-show-popup-article">Подробнее</a>
				</div>
            </div>
			<div style="display:none">
				<div class="article-details wide modal" id="article-details-<?php echo $info->getId();?>">
					<div class="modal-close"></div>
					<div class="article-details-i">
                        <!--<p><img src="/uploads/news/images/<?php echo $img; ?>" alt="<?php echo $info->getQuestion(); ?>"></p>-->

                        <h2><?php echo $info->getQuestion(); ?></h2>
						<?php echo $info->getAnswer(); ?>
                    </div>
				</div>
			</div>
		</div><!-- /list-article -->

        <?php endforeach; ?>

	</div><!-- /list-articles -->

</div><!-- /articles-wrap -->

<?/*
<div>
	<div class="heading dark">
		<h1>FAQ</h1>
	</div>

	<div class="anons-news">
	<?php
		foreach($faqs as $info):
			$img = $info->getImage();
	?>
		<div class="item">
			<div class="date"><?php echo $info->getCreatedAt(); ?></div>

			<div class="content">
				<div class="anons">
					<h3><span><?php echo $info->getQuestion(); ?></span></h3>
					<p class="text"><?php echo Utils::trim_text($info->getAnswer(), 150); ?></p>
				</div>
				<div class="full">
				<?php
					if(!empty($img)):
				?>
					<div class="preview"><img src="/uploads/news/images/<?php echo $img; ?>" alt="<?php echo $info->getQuestion(); ?>"></div>
				<?php endif; ?>

					<h3><span><?php echo $info->getQuestion(); ?></span></h3>
					<p class="text"><?php echo $info->getAnswer(); ?></p>
				</div>
			</div>
			<div class="more"><span></span></div>
		</div>
	<?php endforeach; ?>

	</div>
</div>
*/?>