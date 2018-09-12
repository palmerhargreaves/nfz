<?php
    $sections = ActivityExtendedStatisticSectionsTable::getInstance()->createQuery()->orderBy('id ASC')->execute();

    if($concept):
        foreach($sections as $section):
?>
    <div class="group open">
        <div class="group-header">
            <span class="title"><?php echo $section->getHeader(); ?></span>
        </div>

        <div class="group-content">
            <table class="models">
                <tbody>
                <?php
                $fields = ActivityExtendedStatisticFieldsTable::getInstance()->createQuery()->where('activity_id = ? and parent_id = ?', array($activity->getId(), $section->getId()))->orderBy('order ASC')->execute();

                $n = 0;
                foreach($fields as $field):
                    $fieldValue = $field->getFieldUserValue($activity, $sf_user->getRawValue(), $concept);
                    ?>
                    <?php if($field->getValueType() == ActivityExtendedStatisticFields::FIELD_TYPE_TEXT): ?>
                    <tr class="">
                        <td colspan="2">
                            <strong style='font-size: 12px;'><?php echo $field->getHeader(); ?></strong>
                        </td>
                    </tr>
                <?php else: ?>

                    <tr class="sorted-row model-row<?php if($n++ % 2 == 0) echo ' even'; ?>">

                        <td style="width:605px; font-weight: bold; padding-left: 22px; <?php echo $field->getValueType() == ActivityExtendedStatisticFields::FIELD_TYPE_CALC ? 'background: #D3D3D3' : ''; ?>">
                            <?php
                            echo $field->getHeader();
                            if($field->getDescription())
                                echo sprintf(', (%s)', $field->getDescription());
                            ?>
                        </td>
                        <td class="darker" style='<?php echo $field->getValueType() == ActivityExtendedStatisticFields::FIELD_TYPE_CALC ? 'background: #D3D3D3' : ''; ?>' >
                            <?php
                            if($field->getValueType() == "date") {
                                $period = explode("-", $fieldValue->getValue());

                                ?>
                                <a href="#" class="dates" />
                                <div class="modal-input-wrapper input" style='width: 100px; margin: 7px; float: left;' >
                                    <input type='text' name="periodStart" class='with-date' style='height: 24px; padding: 5px; width: 110px;'  placeholder='От' value="<?php echo $period[0]; ?>"
                                           data-type="<?php echo $field->getValueType(); ?>"
                                           data-regexp="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$"
                                           data-field-id="<?php echo $fieldValue->getId(); ?>" required="true">
                                    <div class="modal-input-error-icon error-icon"></div>
                                    <div class="error message" style='display: none; z-index: 1;'></div>
                                </div>
                                <div class="modal-input-wrapper input" style='width: 124px; margin: 7px; float: right;' >
                                    <input type='text' name="periodEnd" class='with-date' style='height: 24px; padding: 5px; width: 110px;' placeholder='До' value="<?php echo $period[1]; ?>"
                                           data-type="<?php echo $field->getValueType(); ?>"
                                           data-regexp="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$"
                                           data-field-id="<?php echo $fieldValue->getId(); ?>" required="true">
                                    <div class="modal-input-error-icon error-icon"></div>
                                    <div class="error message" style='display: none; z-index: 1;'></div>
                                </div>
                            <?php } else if($field->getValueType() == ActivityExtendedStatisticFields::FIELD_TYPE_CALC) { ?>
                                <strong class='calc-field calc-field-<?php echo $field->getId(); ?>'
                                        data-id='<?php echo $field->getId(); ?>'
                                        data-calc-fields='<?php echo $field->getCalcFields(); ?>'
                                        data-calc-type='<?php echo $field->getCalculateSymbol(); ?>'
                                        data-calc-parent-field='<?php echo $field->getParentCalcField(); ?>'>
                                    <?php echo $field->calculateValue($sf_user); ?>
                                </strong>
                            <?php } else { ?>
                                <?php echo $field->getRequired() ? "<span title='Поле, обязательное для заполнения' style='color: red; float: right; margin: 24px 4px 0px 0px; font-size: 20px;'>*</span>" : ""; ?>
                                <div class="modal-input-wrapper input" style='width: 124px; margin: 7px; float: right;' >

                                    <input type='text' class='field-<?php echo $field->getId(); ?>'
                                           placeholder='0'
                                           style='height: 24px; padding: 5px; width: 110px;'
                                           data-type="<?php echo $field->getValueType(); ?>"
                                        <?php if($field->getValueType() == ActivityExtendedStatisticFields::FIELD_TYPE_VALUE) { ?>
                                            data-regexp="/^[0-9.]+$/"
                                        <?php } else { ?>
                                            data-regexp="/^[0-9a-zA-Zа-яА-Я\_\(\)\+\-\= ]+$/"
                                        <?php } ?>
                                           data-field-id="<?php echo $fieldValue->getId(); ?>"
                                           value="<?php echo $fieldValue->getValue(); ?>"
                                        <?php echo $field->useInCalculate() ? "data-calc-field='true' data-calc-type='".$field->getCalculateSymbol()."' data-calc-parent-field='".$field->getParentCalcField()."'" : ''; ?>
                                        <?php echo $field->getRequired() ? "required" : ""; ?>>

                                    <div class="modal-input-error-icon error-icon"></div>
                                    <div class="error message-modal" style="display: none;"></div>
                                </div>

                            <?php } ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach;
    endif;
?>