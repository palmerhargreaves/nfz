<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 22.09.2016
 * Time: 11:50
 */
?>

<div class="container-user-binded-dealers-<?php echo $user->getId(); ?>">
    <?php if (count($user->getBindedDealersList()) == 0): ?>
        <ul class="sf_admin_actions">
            <li class="sf_admin_action_new">
                <a href="javascript:;" class="action-edit-user-binded-dealers"
                   data-user-id="<?php echo $user->getId(); ?>">Добавить привязку</a>
            </li>
        </ul>
    <?php else:
        foreach ($user->getBindedDealersList() as $item): ?>
            <a href="javascript:;" class="action-edit-user-binded-dealers"
               data-user-id="<?php echo $user->getId(); ?>"><?php echo sprintf('%s (%s)', $item->getDealer()->getNameAndNumber(), $item->getDealer()->getDealerTypeLabel()) ?></a>
            <br/>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
