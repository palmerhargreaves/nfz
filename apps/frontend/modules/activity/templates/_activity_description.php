<table class="description">
    <?php foreach ($activity->getFields() as $field): ?>
        <tr>
            <td class="left-column">
                <div class="relative">
                    <img src="/images/<?php echo $field->getImage(); ?>" alt="">
                    <div class="header"><?php echo $field->getHeader(); ?></div>
                </div>
            </td>

            <td class="content-column">
                <?php if ($field->getType() == "sym"): ?>
                    <ul class="content-column-list">
                        <?php foreach ($field->getFieldData($activity->getId()) as $fieldData): ?>
                            <li><span><?php echo $fieldData->getRawValue()->getDescription(); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <?php
                    $ind = 1;
                    foreach ($field->getFieldData($activity->getId()) as $fieldData):
                        ?>
                        <div class="content-column-list-item">
                            <div class="num"><?php echo $ind++; ?></div>
                            <div class="text"><?php echo $fieldData->getRawValue()->getDescription(); ?></div>
                            <div class="cf"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>