<div class="ask-q">
<?php include_partial('discussion/form_search'); ?>
<?php include_partial('discussion/panel', array('disable_upload' => (isset($disable_upload) && $disable_upload), 
													'outOfDate' => $outOfDate)); ?>
</div>
