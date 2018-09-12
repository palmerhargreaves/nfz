<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Период статистики:
                        <?php echo sprintf('%s / %s',
                            $item1->getPeriodFromDate(),
                            $item1->getPeriodToDate()
                        ); ?>
                    </li>
                </ul>
            </div>
        </div>

        <div class="span6">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Период статистики:
                        <?php echo sprintf('%s / %s',
                            $item2->getPeriodFromDate(),
                            $item2->getPeriodToDate()
                        ); ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span6">
            <div class="well sidebar-nav">

                <?php foreach($comparedItems as $key => $item): ?>
                    <div class="alert alert-info" style="font-size: 13px;">
                        <strong><?php echo sprintf("%s %s", $statuses[$key], $item['left']['stats'][$key]); ?></strong> <?php  ?>
                    </div>

                    <?php
                    if(isset($item['left']['items']) && count($item['left']['items']) > 0): ?>
                        <div class="not-in-compare-list alert alert-warning" style="font-size: 12px; cursor: pointer;" data-status="<?php echo $key; ?>">
                            <?php
                            $activeModels = 0;
                            $deletedModels = 0;

                            $rawItems = $item['left']['items']->getRawValue();

                            foreach($rawItems as $item):
                                if($item[0]['status'] == "deleted") {
                                    $deletedModels++;
                                }
                                else {
                                    $activeModels++;
                                }
                            endforeach;
                            ?>
                            <strong>
                                <?php
                                echo sprintf("Без совпадения: %s<br/> Новых заявок: %s<br/>Удаленных заявок: %s",
                                    count($rawItems),
                                    $activeModels,
                                    $deletedModels);
                                ?>
                            </strong>
                        </div>
                        <div class="not-in-compare-list-<?php echo $key; ?>" style="display: block; width: 100%; margin: 5px; padding: 5px; font-size: 12px; display: none;">
                            <?php
                            foreach($rawItems as $item):
                                if($item[0]['status'] == "deleted") {
                                    echo "<div class='alert alert-error' style='margin: 1px;'>" . sprintf('Заявка №: %s, статус: Удалена', $item[0]['model_id']) . "</div>";
                                }
                                else {
                                    echo "<span style='display: inherit;'>" . sprintf('Заявка №: %s, статус: Активна', $item[0]['model_id']) . "</span>";
                                }
                            endforeach;
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-error" style="font-size: 13px;">
                            <strong><?php echo sprintf("Макетов без совпадения: 0"); ?></strong> <?php  ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="span6">
            <div class="well sidebar-nav">
                <?php foreach($comparedItems as $key => $item): ?>
                    <div class="alert alert-info" style="font-size: 13px;">
                        <strong><?php echo sprintf("%s %s", $statuses[$key], $item['right']['stats'][$key]); ?></strong> <?php  ?>
                    </div>

                    <?php
                    if(isset($item['right']['items']) && count($item['right']['items']) > 0): ?>
                        <div class="not-in-compare-list alert alert-warning" style="font-size: 12px; cursor: pointer;" data-status="<?php echo $key; ?>">
                            <?php
                                $activeModels = 0;
                                $deletedModels = 0;

                                $rawItems = $item['right']['items']->getRawValue();

                                foreach($rawItems as $item):
                                    if($item[0]['status'] == "deleted") {
                                        $deletedModels++;
                                    }
                                    else {
                                        $activeModels++;
                                    }
                                endforeach;
                            ?>
                            <strong>
                                <?php
                                    echo sprintf("Без совпадения: %s<br/> Новых заявок: %s<br/>Удаленных заявок: %s",
                                        count($rawItems),
                                        $activeModels,
                                        $deletedModels);
                                ?>
                            </strong>
                        </div>
                        <div class="not-in-compare-list-<?php echo $key; ?>" style="display: block; width: 100%; margin: 5px; padding: 5px; font-size: 12px; display: none;">
                            <?php
                                foreach($rawItems as $item):
                                    if($item[0]['status'] == "deleted") {
                                        echo "<div class='alert alert-error' style='margin: 1px;'>" . sprintf('Заявка №: %s, статус: Удалена', $item[0]['model_id']) . "</div>";
                                    }
                                    else {
                                        echo "<span style='display: inherit;'>" . sprintf('Заявка №: %s, статус: Активна', $item[0]['model_id']) . "</span>";
                                    }
                                endforeach;
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-error" style="font-size: 13px;">
                            <strong><?php echo sprintf("Макетов без совпадения: 0"); ?></strong> <?php  ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

