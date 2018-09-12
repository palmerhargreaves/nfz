<div class="name"><?php echo $user_name ?></div>
<div class="company">
<?php if($dealer): ?>
  "<?php echo $dealer->getName() ?>"
<?php else: ?>
  <?php echo $group->getName() ?>
<?php endif; ?>
</div>
