<?php
$roles = array();
foreach($user_group->getRoles() as $role)
  $roles[] = $role->getName();
?>
<?php echo implode(', ', $roles) ?>