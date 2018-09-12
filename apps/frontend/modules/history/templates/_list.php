<?php use_helper('History') ?>

<?php foreach ($history as $entry): ?>
    <?php

    $number = 0;
    foreach (ActivityModuleTable::getInstance()->createQuery()->execute() as $module) {
        $number = $module->getHistoryProcessor()->getModelNumber($entry->getRawValue());
    }

    $disabled = in_array($entry->getObjectType(), array('dealer_user', 'register'));

    ?>
    <div
        class="history-item<?php if ($entry->getImportance()) echo ' active' ?><?php if ($disabled) echo ' disabled' ?>">
        <div class="number"><?php echo $number != 0 ? $number : ''; ?>&nbsp;</div>

        <?php if ($disabled || $number == 0): ?>
            <div class="name"><?php echo history_title($entry->getTitle()) ?>&nbsp;</div>
        <?php else: ?>
            <div class="name"><a href="<?php echo url_for('@history_entry?id=' . $entry->getId()) ?>"
                                 title="<?php echo $entry->getTitle() ?>"><?php echo history_title($entry->getTitle()) ?></a>&nbsp;
            </div>
        <?php endif; ?>
        <div class="project">
            <?php if ($entry->getUser()): ?>
                <?php if ($entry->getUser()->isDealerUser()): ?>
                    <?php echo $entry->getUser()->getDealer()->getName() ?>
                <?php else: ?>
                    <?php echo $entry->getUser()->getGroup()->getName() ?>
                <?php endif; ?>
            <?php elseif ($entry->getDealerId() && $entry->getDealer()): ?>
                <?php echo $entry->getDealer()->getName() ?>
            <?php else: ?>
                Удалён
            <?php endif; ?>
        </div>
        <div class="message"><?php echo $entry->getDescription() ?></div>
        <div class="icon <?php echo $entry->getIcon() ?>"></div>
        <div class="date"><?php echo D::toShortRus($entry->created_at, true) ?></div>
        <div class="time"><?php echo date('H:i', D::toUnix($entry->created_at)) ?></div>
        <div class="cf"></div>
    </div>
<?php endforeach; ?>
