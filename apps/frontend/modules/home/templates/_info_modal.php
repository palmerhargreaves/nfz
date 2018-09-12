<div id="info-modal" class="modal" style="width: <?php echo $data->getWidth() ? $data->getWidth() : '500'; ?>px;">
    <div class="modal-header"><?php echo $data->getRawValue()->getHeader(); ?></div>
    <div class="modal-close"></div>
    <div class="modal-text">
    <?php 
    	$description = $data->getRawValue()->getDescription();
    	$encode = $sf_user->getAuthUser()->getEncodedToken();
		if($encode):
			$description = preg_replace_callback('#\[survey_link](.+?)\[\/survey_link\]#s', 
												function($matches) use($encode) {
													return "<a target='_blank' href='http://survey.vw-servicepool.ru/?oitokenauth=".urlencode( $encode )."'>{$matches[1]}</a>";
												}, 
												$description);
		endif;
    	
    	echo $description; ?>
    </div>        
</div>
