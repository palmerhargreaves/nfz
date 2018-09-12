<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 25.12.2015
 * Time: 14:04
 */
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">
                        Должности:
                        <span><?php echo count($postLimits); ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="span6">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Пользователи:
                        <span><?php echo count($usersLimits); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span6">
            <div class="well sidebar-nav">
                <select id="sbDialogLimitUserPost" multiple size="15" style="width: 320px;" data-dialog-id="<?php echo $dialogId; ?>">
                <?php foreach($usersPosts as $post): ?>
                    <option value="<?php echo $post->getId(); ?>" <?php echo in_array($post->getId(), $postLimits->getRawValue()) ? "selected" : ""; ?>><?php echo $post->getName();?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="span6">
            <div class="well sidebar-nav">
                <input type="text" class="input" placeholder="Фильтр: Фамилия / Имя" id="txtFitlerSurnameName" />
                <select id="sbDialogLimitUsers" multiple size="15" style="width: 320px;" data-dialog-id="<?php echo $dialogId; ?>">
                    <?php foreach($users as $user): ?>
                        <option value="<?php echo $user->getId(); ?>" <?php echo in_array($user->getId(), $usersLimits->getRawValue()) ? "selected" : ""; ?>
                                data-surname="<?php echo $user->getSurname(); ?>" data-name="<?php echo $user->getName(); ?>">
                            <?php echo sprintf('%s %s', $user->getSurname(), $user->getName()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <a href="javascript:;" style="font-size: 12px; color: #08c; margin-left: 10px; " class="on-click-unselect-items">Снять выделенное</a>
            </div>
        </div>
    </div>
</div>


