<?php
if (!is_array($role))
    $role = array($role);

$role[] = 'admin';

if (empty($action))
    $action = 'index';

?>
<?php if ($sf_user->hasCredential($role, false)): ?>
    <li<?php if ($sf_context->getModuleName() == $module): ?> class="active"<?php endif; ?>><a
            href="<?php echo url_for($module . '/' . $action) ?>"><?php echo $name ?></a></li>
<?php endif; ?>
