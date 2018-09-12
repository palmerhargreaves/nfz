<form method="post" action="/">
    <input type="hidden" name="id">
    <table>
        <?php foreach ($specialist_groups as $group): ?>
            <?php $active_users = $group->getActiveUsers() ?>
            <?php if ($active_users->count() > 0): ?>
                <tr class="group-row" data-group="<?php echo $group->getId() ?>">
                    <td class="check"><input type="checkbox" name="specialist[group][<?php echo $group->getId() ?>]">
                    </td>
                    <td class="label">
                        <?php echo $group->getName() ?>
                    </td>
                    <td width="350">
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
                        <div class="value"></div>
                    </td>
                    <td>
                        <div title="Добавить сообщение" class="msg-button btn">
                            <div class="ico"></div>
                        </div>
                    </td>
                </tr>
                <tr class="specialist-message">
                    <td colspan="3">
                        <textarea name="specialist[msg][<?php echo $group->getId() ?>]"
                                  placeholder="Введите комментарий..."></textarea>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
    <div class="btn-group">
        <button class="button btn-primary" type="submit">Отправить</button>
        <button class="gray button cancel-btn" type="button">Отмена</button>
        <div class="clear"></div>
    </div>

</form>