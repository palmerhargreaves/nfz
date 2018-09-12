<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 15.12.2015
 * Time: 11:48
 */
?>
<table class="table table-bordered table-striped table-hover" id="table-dealers-users">
    <thead>
    <tr>
        <th>#</th>
        <th>Пользователь</th>
        <th>Должность</th>
        <th>Дилер</th>
        <th style="text-align: center;">Разрешен</th>
        <th style="text-align: center;">Менеджер</th>
        <th>Создан</th>
        <th>Обновлен</th>
        <th style="text-align: center;">Действия</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $ind = 1;
    foreach ($usersWithDealers as $userDealer):
        $users = DealerUserTable::getInstance()->createQuery('du')->innerJoin('du.User u')->where('dealer_id = ?', $userDealer->getDealerId())->execute();
        foreach ($users as $user):
            ?>
            <tr id="item-index-<?php echo $user->getId(); ?>" class="user-dealer-item" data-has-manager="<?php echo $user->getManager() ? "yes" : "no"; ?>"
                data-is-allowed="<?php echo $user->getApproved() ? "yes" : "no"; ?>"
                data-user-email="<?php echo $user->getUser()->getEmail(); ?>"
                data-user-name="<?php echo mb_strtolower($user->getUser()->getName()); ?>"
                data-user-surname="<?php echo mb_strtolower($user->getUser()->getSurname()); ?>"
                data-dealer-id="<?php echo $userDealer->getDealerId(); ?>">
                <td><?php echo $ind++; ?></td>
                <td><?php echo sprintf('[%s] %s %s', $user->getUser()->getGroup()->getName(), $user->getUser()->getSurname(), $user->getUser()->getName()); ?></td>
                <td><?php echo $user->getUser()->getPost() ?></td>
                <td><?php echo $user->getDealer()->getName(); ?></td>
                <td style="text-align: center;">
                    <a href="javascript:;" class="on-change-approve-status" data-id="<?php echo $user->getId(); ?>">
                        <span
                            class="label label-<?php echo $user->getApproved() ? "success" : "error"; ?>"><?php echo $user->getApproved() ? "Да" : "Нет"; ?></span>
                    </a>
                </td>
                <td style="text-align: center;">
                    <a href="javascript:;" class="on-change-manager-status" data-id="<?php echo $user->getId(); ?>">
                        <span
                            class="label label-<?php echo $user->getManager() ? "success" : "error"; ?>"><?php echo $user->getManager() ? "Да" : "Нет"; ?></span>
                    </a>
                </td>
                <td><?php echo $user->getCreatedAt(); ?></td>
                <td><?php echo $user->getUpdatedAt(); ?></td>
                <td style="text-align: center;">
                    <a href="javascript:;" class="on-delete-dealer-user" data-id="<?php echo $user->getId(); ?>">
                        <img data-id="<?php echo $user->getId(); ?>" class="remove-user-dealer-record" style="cursor: pointer;" src="/images/delete-icon.png" title="Удалить запись" />
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>