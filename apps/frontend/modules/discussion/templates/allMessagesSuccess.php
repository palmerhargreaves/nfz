<div class="approvement">
  <h1>Сообщения</h1>

  <table width="100%">
      <tr>
          <td><?php include_partial('global/paginator', $pager_paginatorData); ?></td>
      </tr> 
  </table> 
  <hr /> 
                     
    <form action="" method="get"
              data-url-list='<?php echo url_for('@discussion_special_messages_list'); ?>'
              data-url-post='<?php echo url_for('@discussion_special_message_add'); ?>'>
      <table class="models" id="messages-list">
          <thead>
              <tr>
              	  <td width="35"><div class="has-sort">№</div><div class="sort has-sort"></div></td>
                  <td width="150"><div class="has-sort">Дилер / Email</div><div class="sort has-sort"></div></td>
                  <!--<td width="146"><div>Период</div></td>-->
                  <td width="81">Имя</td>
                  <td width="350"><div>Сообщение</div></td>
                  <td width="50"><div>Статус</div></td>
                  <td width="50"></td>
              </tr>
          </thead>

          <tbody>

          <?php 
            $n = 1;

            $messages = $pager->getResults();
            foreach($messages as $message): 
                $user = $message->getUser();

                if(!$user)
                  continue;

                $dealer = $user->getDealerUsers()->getFirst();
                if(!$dealer)
                  continue;

                $dealer = $dealer->getDealer();
                $model = $message->getModel();
          ?>
            <tr class="sorted-row model-row<?php if($n % 2 == 0) echo ' even' ?>" >
            	<?php
            		if($model) {
            	?>
            	<td data-sort-value="<?php echo $model->getId() ?>">
            		<?php echo "<a href=".url_for('@discussion_switch_to_dealer?dealer='.$dealer->getId().'&activityId='.$model->getActivityId().'&modelId='.$model->getId())." target='_blank'>".$model->getId()."</a>"; ?>
            	</td>
            	<?php } else { ?>
				<td ></td>
            	<?php } ?>
                <td data-sort-value="<?php echo $dealer->getNumber(); ?>">
                <?php echo sprintf('<strong>%s (%s)</strong><br/>%s', substr($dealer->getNumber(), 5), $dealer->getName(), $user->getEmail()); ?>
                </td>
                <td ><?php echo $message->getUserName() ?></td>
                <td >
                <?php 
                	$text = $message->getText();
                  
                	$res = strpos($text,"file:///");
                	if($res == 0 && is_numeric($res))
						        $text =  Utils::trim_text($text, 75);

                	echo $text; 
                ?>
                </td>
                <?php
                  $cssStatus = $message->getDiscussion()->isLastMessageNew() ? "pencil" : "ok";
                  if($message->isReaded() && $cssStatus == "ok")
                    $cssStatus = "ok";
                ?>
                <td ><div class="<?php echo $cssStatus; ?>" style="width: 30px;"></div></td>
                <td >
                    <input type="button" class="button small special-discussion-button" value="Ответить" 
                                data-message-user-id="<?php echo $user->getId(); ?>"
                                data-discussion-id="<?php echo $message->getDiscussion()->getId();?>"
                                data-user-id="<?php echo $sf_user->getAuthUser()->getId(); ?>">
                </td>
            </tr>
            
          <?php $n++; endforeach; ?>
          </tbody>
    </table>
  </form>
  <hr/>
  <table width="100%">
      <tr>
          <td><?php include_partial('global/paginator', $pager_paginatorData); ?></td>
      </tr> 
  </table> 
</div>
  
<script>
  $(function() {
    window.special_discussion = new SpecialDiscussion({
          post_url: "<?php echo url_for('@discussion_special_message_add') ?>",
          panel: ".panel-special-message",
          uploader: new Uploader({
            selector: '#special-modal .message-upload',
            session_name: '<?php echo session_name() ?>',
            session_id: '<?php echo session_id() ?>',
            upload_url: '/upload.php',
            delete_url: "<?php echo url_for('@upload_temp_delete') ?>"
          }).start()
        }).start();
  });
</script>
  