<?php
	$info = $item['item'];

	$img = $info->getImgBig();
	if(empty($img))
		$img = $info->getImgSmall();
?>
<div style="display: block;">
	<div class="anons-news">
		<div class="item active" style="float: left;">
			<div class="date"><?php echo $info->getCreatedAt(); ?></div>

			<div class="content">
				<div class="full" style="display: block; height: auto;">
				<?php if($item['isNew']): ?>
			        <img class='anons-new' src='/images/news_64x50.png' title='<?php echo $info->getName(); ?>' />
			    <?php endif; ?>
					<div class="preview" style='display: block; float:left;'><img src="/uploads/news/images/<?php echo $img; ?>" alt="<?php echo $info->getName(); ?>"></div>
					<div style='display: block; width: 100%;'>
						<h3><span><?php echo $info->getName(); ?></span></h3>
						<p class="text"><?php echo $info->getRawValue()->getText(); ?></p>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>

<?php
	if(count($last10News) > 0): 
?>
<div style='display: inline-table; width: 100%;'>
	<div class="heading dark">
		<h1>Последние 10 новостей</h1>
	</div>

	<div class="anons-news">
	<?php
		foreach($last10News as $it):
			include_partial('item', array('info' => $it['item'], 'item' => $it));
	?>
	<?php endforeach; ?>

	</div>
</div>
<?php endif; ?>
