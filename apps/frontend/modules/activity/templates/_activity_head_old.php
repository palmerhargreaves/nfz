<a href="<?php echo url_for('home/index') ?>" class="small back button">Назад</a>

<div class="activity-header-wrapper">
    <div class="activity-header">
            <div class="num"><?php echo $activity->getId() ?></div>
            <div class="title"><?php echo $activity->getName() ?></div>
            <div class="date">
            с <?php echo D::toLongRus($activity->getStartDate()) ?>
            <br>
            по <?php echo D::toLongRus($activity->getEndDate()) ?>
            </div>
            
            <div class="activity-status-ico">
              <div class="img-wrapper">
                <?php 
                  $status_icon = null; 
                  switch($activity->getStatus($sf_user->getRawValue()->getAuthUser())) {
//                    case ActivityModuleDescriptor::STATUS_IMPORTANCE;
//                      $status_icon = 'warn-icon.png';
//                      break;
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
                <img src="/images/<?php echo $status_icon ?>" alt="">
<?php endif; ?>
              </div>
            </div>
    </div>

    <div class="stages-wrapper" id="activity-stages">
    <?php
        $activity->callWithModule(function(ActivityModuleDescriptor $descriptor) use($activity, $sf_user) {
        $count = $activity->getTasks()->count();
        $additional = $descriptor->getActivityAdditional();

        echo $additional;
        }, $sf_user->getAuthUser()->getRawValue());
		
    ?>
    <?php foreach($activity->getTasks() as $n => $task): 

	?>
        <div  class="stage<?php if($sf_user->isDealerUser() && $task->wasDone($sf_user->getAuthUser()->getDealer()->getRawValue())) echo ' active' ?>"><?php echo $task->getName() ?></div>
    <?php endforeach; ?>
    </div>
</div>
<script type="text/javascript">
  $(function() {
    $('#activity-stages .stage:last').addClass('last');
  });
</script>