<?php echo $activity->getName() ?> 
<?php 
$configurable_modules = array();
foreach($activity->getModules() as $module)
{
  $descriptor = $activity->getModuleDescriptor($module->getRawValue(), $sf_user->getAuthUser()->getRawValue());
  if($descriptor->hasAdditionalConfiguration())
    $configurable_modules[] = array('module' => $module, 'descriptor' => $descriptor);
}
?>
<?php if($configurable_modules): ?>
<table cellspacing="0">
  <thead>
    <tr>
      <th>
        Настройки модулей:
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
  <?php foreach($configurable_modules as $m): ?>
        <ul class="sf_admin_actions">
          <li class="sf_admin_action_edit">
            <a href="<?php echo url_for($m['descriptor']->getAdditionalConfigurationUri()) ?>"><?php echo $m['module']->getName() ?></a>    
          </li>
        </ul>
  <?php endforeach; ?>
      </td>
    </tr>
  </tbody>
</table>
<?php endif; ?>
