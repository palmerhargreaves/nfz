<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 19.04.2016
 * Time: 10:41
 */
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <table class="table table-striped">
                    <?php
                    $ind = 1;
                    foreach($history_items as $item): ?>
                        <tr>
                            <td><?php echo $ind++; ?></td>
                            <td><?php echo $item->getType() == "blocked" ? "<span class='label label-warning'>Заблокирована</span>" : "<span class='label label-success'>Разблокирована</span>"; ?></td>
                            <td>
                                <?php
                                    if ($item->getUserId() != -1) {
                                        $user = UserTable::getInstance()->findOneById($item->getUserId());
                                        if($user) {
                                            echo sprintf('%s %s', $user->getSurname(), $user->getName());
                                        }
                                    } else {
                                        echo "-";
                                    }
                                ?>
                            </td>
                            <td><?php echo $item->getCreatedAt(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>
