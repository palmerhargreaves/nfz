<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 12.07.2016
 * Time: 12:20
 */

if (!isset($start_from) || $start_from == 0): ?>
    <form action="" method="get"
    data-url-list='<?php echo url_for('@discussion_special_messages_list'); ?>'
    data-url-post='<?php echo url_for('@discussion_special_message_add'); ?>'>
    <table class="models" id="messages-list">
    <thead>
    <tr>
        <td width="35">
            <div class="has-sort">№</div>
            <div class="sort has-sort"></div>
        </td>
        <td width="150">
            <div class="has-sort">№ дилера</div>
            <div class="sort has-sort"></div>
        </td>
        <!--<td width="146"><div>Период</div></td>-->
        <td width="81">Имя</td>
        <td width="350">
            <div>Сообщение</div>
        </td>
        <td width="50"></td>
    </tr>
    </thead>
    <tbody>
<?php endif; ?>

<?php
$n = 1;

if ($pager) {
    $messages = $pager->getResults();
}
foreach ($messages as $message):
    if (!($user = $message->getUser())) {
        continue;
    }

    if ($dealer = $user->getDealerUsers()->getFirst()) {
        $dealer = $dealer->getDealer();
    }

    $model = $message->getModel();
    if (isset($message_type) && $message_type == Discussion::MODELS_MESSAGES) {
        if (!$dealer) {
            continue;
        }
    }
    ?>
    <tr class="sorted-row model-row<?php if ($n % 2 == 0) echo ' even' ?>"
        data-item-id="<?php echo $message->getId(); ?>">
        <?php if ($model) { ?>
            <td data-sort-value="<?php echo $model->getId() ?>" style="width: 30px;">
                <?php if ($dealer): ?>
                    <?php echo "<a href=" . url_for('@discussion_switch_to_dealer?dealer=' . $dealer->getId() . '&activityId=' . $model->getActivityId() . '&modelId=' . $model->getId()) . " target='_blank'>" . $model->getId() . "</a>"; ?>
                <?php endif; ?>
            </td>
        <?php } else { ?>
            <td style="width: 30px;"></td>
        <?php } ?>
        <td data-sort-value="<?php echo $dealer ? $dealer->getNumber() : 0; ?>" style="width: 50px;">
            <?php if ($dealer): ?>
                <?php echo sprintf('<strong>%s</strong>', substr($dealer->getNumber(), 5)); ?>
            <?php endif; ?>
        </td>
        <td style="width: 100px;"><?php echo $message->getUserName() ?></td>
        <td style="width: 300px;">
            <?php
            $text = $message->getText();
            if (($res = strpos($text, "file:///")) == 0 && is_numeric($res)) {
                $text = Utils::trim_text($text, 75);
            }
            echo $text;
            ?>
        </td>

        <td style="width: 50px;">
            <input type="button" class="button small special-discussion-button" value="Ответить"
                   data-message-user-id="<?php echo $user->getId(); ?>"
                   data-message-id="<?php echo $message->getId(); ?>"
                   data-discussion-id="<?php echo $message->getDiscussion()->getId(); ?>"
                   data-user-id="<?php echo $sf_user->getAuthUser()->getId(); ?>"
                   data-mark-as-read="<?php echo isset($page_parent) && $page_parent == Discussion::PAGER_NEW_MESSAGES ? 1 : 0; ?>">
        </td>
    </tr>

    <?php $n++; endforeach; ?>

<?php if (!isset($start_from) || $start_from == 0): ?>
    </tbody>
    </table>
    </form>
<?php endif; ?>

<?php if ($paginatorData): ?>
    <hr/>
    <table width="100%">
        <tr>
            <td><?php include_partial('global/paginator', $paginatorData); ?></td>
        </tr>
    </table>
<?php endif; ?>