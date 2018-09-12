<div class="articles-wrap">
	<h1>Все новости</h1>

	<div class="list-articles d-cb">
        <?php if($selected_news_item): ?>
        <div class="list-article featured">
            <?php include_partial('item', array('info' => $selected_news_item, 'read_next' => null));?>
        </div>
        <?php endif;

		foreach($news as $key => $item): ?>
		<div class="list-article<?php echo $key == 0 && !isset($selected_news_item) ? " featured" : "";?>">
			<?php include_partial('item', array('info' => $item['item'], 'read_next' => ($key == 0 ? false : false)));?>
		</div><!-- /list-article -->
	<?php endforeach; ?>

	</div>
</div>
