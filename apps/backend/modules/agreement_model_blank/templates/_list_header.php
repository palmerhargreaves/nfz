<?php
$activity = ActivityTable::getInstance()->find($sf_user->getAttribute('activity_id', 0, 'agreement_module'));
?>
<?php if($activity): ?>
<h1>Болванки макетов для активности<br>"<?php echo $activity->getName() ?>"</h1>
<?php endif; ?>
