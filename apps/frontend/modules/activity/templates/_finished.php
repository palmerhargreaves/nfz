<div class="finished">
<?php //include_partial('activity/activities', array('activities' => $activities, 'title' => '', 'onlyShow' => $onlyShow)); ?>
<div class="activities-list" style="margin-top: 5px; <?php echo empty($description) ? 'padding-top: 1px;' : '' ?>"  >
        <h1><?php echo $title; ?></h1>
        <?php if(!empty($description)) : ?>
        <p><?php echo $sf_data->getRaw('description') ?></p>
        <?php endif; ?>

  

<?php foreach($activities as $activity): 
		    if(!$activity->isActiveForUser($sf_user->getRawValue()->getAuthUser()))
			   continue;

        if(!$activity->getFinished())
          continue;
	?> 
       <a href="<?php echo url_for('activity/index?activity='.$activity['id']) ?>" class="activity<?php if($activity['is_viewed']) echo ' closed' ?>" >
                <div class="corner"></div>
                <div class="num"><?php echo $activity['id'] ?></div>
                <div class="date">
  <?php if($activity['custom_date']): ?>
                  <?php echo nl2br($activity['custom_date']) ?>
  <?php else: ?>
                  c <?php echo D::toLongRus($activity['start_date']) ?>
                  <br>
                  по <?php echo D::toLongRus($activity['end_date']) ?>
  <?php endif; ?>
                </div>
                <div class="text">
                    <div class="title"><?php echo $activity['name'] ?></div>
                    <div class="desc"><?php echo $activity->getRaw('brief') ?></div>
                </div>
                <div class="activity-status-ico">
                  <div class="img-wrapper<?php if(!$activity['is_viewed']) echo ' border'; ?>">
                    <?php 

                      $status_icon = null; 
                      $status_icon_title = '';
                      switch($activity->getStatus($sf_user->getRawValue()->getAuthUser())) {
//                        case ActivityModuleDescriptor::STATUS_IMPORTANCE;
//                          $status_icon = 'warn-icon.png';
//                          $status_icon_title = 'выполнение данной активности влияет на получение бонуса по маркетингу сервиса';
//                          break;
                        case ActivityModuleDescriptor::STATUS_ACCEPTED:
                          $status_icon = 'ok-icon-active.png';
                          break;
                        case ActivityModuleDescriptor::STATUS_WAIT_AGREEMENT:
                          $status_icon = 'wait-icon.png';
                          break;
                        case ActivityModuleDescriptor::STATUS_WAIT_DEALER:
                          $status_icon = 'pencil-icon.png';
                          break;
                      }
                    ?>
  <?php if($status_icon): ?>
                    <img src="/images/<?php echo $status_icon ?>" alt="<?php echo $status_icon_title ?>" title="<?php echo $status_icon_title ?>">
  <?php endif; ?>

                  <?php if($activity['select_activity'] == 1): ?>
                    <img src="/images/warn-icon.png" alt="<?php  ?>" title="<?php ?>">      
                  <?php endif; ?>
                  </div>
                </div>
            </a>
        <div class="clear"></div>
<?php endforeach; ?>
</div>

</div>