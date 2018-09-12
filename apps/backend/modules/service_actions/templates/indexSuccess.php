<h1>Статистика по Сервисным акциям</h1>

<form id='frmFilterData' action='<?php echo url_for('service_filter_data'); ?>' method='post'>
    <div style="display: block; width: 35%">
        <table class="table table-bordered table-striped " cellspacing="0">
            <tr>
                <td class="span3">Сервисная акция</td>
                <td class="span3">
                    <select name='sb_service_action'>
                        <option value='-1'>Выберите акцию ...</option>
                        <?php
                        foreach ($serviceActions as $service) {
                            echo '<option value="' . $service->getId() . '" ' . ($service->getId() == $serviceDialogId ? 'selected' : '') . '>' . $service->getHeader() . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="span3">Активности</td>
                <td class="span3">
                    <select name='sb_activities'>
                        <option value='-1'>Выберите активность ...</option>
                        <?php
                        foreach ($activities as $activity) {
                            echo '<option value="' . $activity->getId() . '" ' . ($activity->getId() == $activityFilterId ? 'selected' : '') . '>' . $activity->getName() . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="span3">Дилеры</td>
                <td class="span3">
                    <select name='sb_dealers'>
                        <option value='-1'>Выберите дилера ...</option>
                        <?php
                        foreach ($dealers as $dealer) {
                            $number = substr($dealer->getNumber(), -3);
                            echo '<option value="' . $dealer->getId() . '" ' . ($dealer->getId() == $dealerId ? 'selected' : '') . '>' . sprintf("%s - %s", $number, $dealer->getName()) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="span3">Не участвуют</td>
                <td class="span3">
                    <input type='checkbox' value='1' name='chDeclinedDealers'
                           id='chDeclinedDealers' <?php echo isset($isDeclined) && $isDeclined == 1 ? 'checked' : ''; ?>>
                </td>
            </tr>

            <tr>
                <td class="span6" colspan='2'>
                    <input type='button' class='btn' style='float: right; margin-right: 10px;' value='Очистить'
                           data-url="<?php echo url_for('service_filter_reset'); ?>">
                    <input type='submit' class='btn' style='float: right; margin-right: 10px;' value='Фильтр'>
                </td>
            </tr>
        </table>
    </div>
</form>

<?php if (isset($result)): ?>
    <div class="alert alert-info">
        <?php if (!$isDeclined): ?>
            <strong>Дилеров с активированной акцией:</strong> <?php echo count($result); ?>
        <?php else: ?>
            <strong>Дилеров с отмененной акцией:</strong> <?php echo count($result); ?>
        <?php endif; ?>
    </div>

    <a href='<?php echo url_for('service_add'); ?>'>Добавить</a>
    <table class="table table-bordered table-striped " cellspacing="0">
        <thead>
        <?php if (!$isDeclined): ?>
            <tr>
                <th>#</th>
                <th>№ Дилера</th>
                <th>Дилер</th>
                <th>Активность</th>
                <th>Сервисная Акция</th>
                <th>Дата начала</th>
                <th>Дата окончания</th>
                <th>Дата подтверждения</th>
                <th>Действия</th>
            </tr>
        <?php else: ?>
            <tr>
                <th>#</th>
                <th>№ Дилера</th>
                <th>Дилер</th>
                <th>Активность</th>
                <th>Сервисная Акция</th>
                <th>Дата отмены акции</th>
                <th>Действия</th>
            </tr>
        <?php endif; ?>
        </thead>

        <?php
        $ind = 1;
        foreach ($result as $item) {
            if (!$isDeclined):
                ?>
                <tr>
                    <td class="span1"><?php echo $ind++; ?></td>
                    <td class="span2"><?php echo $item->getDealer()->getShortNumber(); ?></td>
                    <td class="span3"><?php echo $item->getDealer()->getName(); ?></td>
                    <td class="span4"><?php echo $item->getDialog()->getActivity()->getName(); ?></td>
                    <td class="span3"><span><?php echo $item->getDialog()->getHeader(); ?></span></td>
                    <td class="span3"><span><?php echo $item->getStartDate() ?></span></td>
                    <td class="span3"><span><?php echo $item->getEndDate() ?></span></td>
                    <td class="span3"><span><?php echo $item->getCreatedAt() ?></span></td>
                    <td class="span1" style='text-align: center;'><img src='/images/delete-icon.png'
                                                                       style='cursor:pointer;' title='Удалить'
                                                                       class='delete-service-action'
                                                                       data-id='<?php echo $item->getId(); ?>'/></td>
                </tr>
                <?php
            else:
                ?>
                <tr>
                    <td class="span1"><?php echo $ind++; ?></td>
                    <td class="span3"><?php echo $item->getDealer()->getName() . "(" . $item->getDealer()->getNumber() . ')'; ?></td>
                    <td class="span4"><?php echo $item->getDialog()->getActivity()->getName(); ?></td>
                    <td class="span3"><span><?php echo $item->getDialog()->getHeader(); ?></span></td>
                    <td class="span3"><span><?php echo $item->getCreatedAt() ?></span></td>
                    <td class="span1" style='text-align: center;'><img src='/images/delete-icon.png'
                                                                       style='cursor:pointer;' title='Удалить'
                                                                       class='delete-service-action'
                                                                       data-id='<?php echo $item->getId(); ?>'/></td>
                </tr>
                <?php
            endif;
        }
        ?>
    </table>

<?php endif; ?>

<script>
    $(function () {
        $('input[type=button]').click(function () {
            $('#frmFilterData').attr('action', $(this).data('url')).submit();
        });

        $('.delete-service-action').click(function () {
            if (confirm('Удалить запись ?')) {
                var $el = $(this);
                $.post("<?php echo url_for('service_delete_item'); ?>",
                    {id: $el.data('id')},
                    function (result) {
                        $el.closest('tr').remove();
                    });
            }
        });
    });
</script>