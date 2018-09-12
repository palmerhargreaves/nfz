<table>
    <?php
    $groups = arraY();
    foreach ($specialist_groups as $group) {
        $groups[] = $group;
    }
    $specialist_groups = array_reverse($groups);

    foreach ($specialist_groups as $group): ?>
        <?php $active_users = $group->getActiveUsers() ?>
        <?php if ($active_users->count() > 0): ?>
            <tr class="group-row" data-group="<?php echo $group->getId() ?>">
                <td class="check"><input type="checkbox" name="specialist[group][<?php echo $group->getId() ?>]">
                </td>
                <td class="label">
                    <?php echo $group->getName() ?>
                    <div class="modal-select-wrapper select krik-select input">
                        <span class="select-value"><?php echo $active_users->getFirst()->selectName() ?></span>
                        <input type="hidden" name="specialist[user][<?php echo $group->getId() ?>]"
                               value="<?php echo $active_users->getFirst()->getId() ?>">
                        <div class="modal-input-error-icon error-icon"></div>
                        <div class="error message"></div>
                        <div class="ico"></div>
                        <div class="modal-select-dropdown">
                            <?php foreach ($active_users as $user): ?>
                                <div class="modal-select-dropdown-item select-item"
                                     data-value="<?php echo $user->getId() ?>"><?php echo $user->selectName() ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
