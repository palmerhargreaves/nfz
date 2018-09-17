<div class="activity">
<?php include_partial('activity/activity_head', array('activity' => $activity, 'current_q' => $current_q, 'current_year' => $current_year)) ?>
	<div class="content-wrapper">
		<?php include_partial('activity/activity_tabs', array('activity' => $activity, 'active' => 'materials')) ?>
        <div class="pane-shadow"></div>
        <div class="pane clear">
			<div id="materials" class="active">
				<?php $cat_ids = array_keys($activities->getRawValue()->getMaterials()); ?>
				<script type="text/javascript">
				$(function(){
					$('.js-materials-aside .materials-tab').on('click',function(){
						var href = $(this).data('href');
						$('.js-materials-aside .materials-tab, .js-materials-group').removeClass('active');
						$(this).addClass('active');
						$(href).addClass('active');
						return false;
					});
				});
				</script>

				<div class="materials-aside js-materials-aside">
				<?php foreach($activities->getMaterials() as $id => $category): ?>
					<div class="materials-tab<?php if($id == $cat_ids[0]) echo ' active' ?>" data-href="#material-group-<?php echo $id ?>"><?php echo $category['category'] ?></div>
				<?php endforeach; ?>
				</div><!-- /materials-aside -->

				<?php foreach($activities->getMaterials() as $id => $category): ?>
					<div class="group<?php if($id == $cat_ids[0]) echo ' active' ?> js-materials-group" id="material-group-<?php echo $id ?>">
						<div class="group-header"><span><?php echo $category['category'] ?></span><div class="group-header-toggle"></div></div>
						<div class="group-content">
						<?php foreach($category['materials'] as $n => $m): ?>
							<div class="banner<?php if(($n + 1) % 6 == 0) echo ' sixth'; ?><?php if($activities->isViewed($m->getId())) echo ' closed' ?> banner-<?php echo $m->getId() ?>" data-material="<?php echo $m->getId() ?>">
								<div class="corner"></div>
								<?php if($m->getFirstPreview()): ?>
								<div class="image-wrapper"><img src="/uploads/materials/web_preview/preview/<?php echo $m->getFirstPreview()->getFile() ?>"<?php if($m->getFirstPreview()->isLandscape()) echo ' class="landscape"' ?> alt="<?php echo $m->getName() ?>"></div>
								<?php endif; ?>
								<div class="desc"><?php echo $m->getName() ?></div>
							</div>
						<?php endforeach; ?>
							<div class="clear"></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<?php include_partial('window') ?>

<script type="text/javascript">
$(function() {
  new MaterialsListController({
    list_selector: '#materials',
    win: new MaterialWindow({
      selector: '#zoom',
      url: '<?php echo url_for("@activity_materials_item?activity=".$activity->getId())?>'
    }).start()
  }).start();
})
</script>
