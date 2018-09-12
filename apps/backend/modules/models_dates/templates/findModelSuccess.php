<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Результат поиска, найдено - [<?php echo count($models); ?>]</li>
                </ul>
            </div>
        </div>
    </div>

    <?php if ($models): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="well sidebar-nav">
                    <div class="alert alert-warning">
                        Список заявок
                    </div>
                    <table class="table table-hover table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style='width: 1%;'>#</th>
                            <th>№ Заявки</th>
                            <th>Название</th>
                            <th>Активность</th>
                            <th>Дилер</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $ind = 1;
                        foreach ($models as $model):
                            ?>
                            <tr>
                                <td><input type="checkbox" class="model-index"
                                           data-model-id="<?php echo $model->getId(); ?>"/></td>
                                <td><?php echo $model->getId(); ?></td>
                                <td><?php echo $model->getName(); ?></td>
                                <td><?php echo $model->getActivity()->getName(); ?></td>
                                <td><?php echo $model->getDealer()->getName(); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <div class="well sidebar-nav">
                    <div class="alert alert-warning">
                        Перенос заяв(ки,ок) в:
                    </div>

                    <div class="alert container-move-result" style="display: none;"></div>

                    <form
                        action="<?php echo $moveType == models_datesActions::MOVE_TYPE_ACTIVITIES ? url_for('model_date') : url_for('model_move_to_dealer'); ?>"
                        method="get" class="form-inline" id="model-dates-form">
                        <?php if ($moveType == models_datesActions::MOVE_TYPE_ACTIVITIES): ?>
                            <select name='sbActivity'>
                                <option value='-1'>Выберите активность ...</option>
                                <?php foreach ($activities as $activity): ?>
                                    <option
                                        value='<?php echo $activity->getId(); ?>'><?php echo sprintf('[%s] %s', $activity->getId(), $activity->getName()); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="model_date" placeholder="дата выполнения заявки" value=""
                                   class="input date">
                        <?php else: ?>
                            <select name='sbDealer'>
                                <option value='-1'>Выберите дилера ...</option>
                                <?php foreach ($dealers as $dealer): ?>
                                    <option
                                        value='<?php echo $dealer->getId(); ?>'><?php echo sprintf('[%s] %s', $dealer->getNumber(), $dealer->getName()); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                        <input type="submit" value="Изменить" class="btn">
                        <input type="hidden" name="moveType" value="<?php echo $moveType; ?>"/>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    $('#model-dates-form input.date').datepicker({dateFormat: "dd-mm-yy"});

    $("input[type=submit]").click(function (e) {
        e.preventDefault();

        var moveType = $("input[name=moveType]").val(), moveTo = '', modelsIds = [], data = {}, valid = true, $bt = $(this);

        if (moveType == 'activity') {
            data.moveTo = $('select[name=sbActivity]').val();
            data.modelToDate = $('input[name=model_date]').val();

            if (data.modelToDate.length == 0 && data.moveTo == -1) {
                alert('Выбрите активность или дату для продолжения.');
                valid = false;
            }
        } else {
            data.moveTo = $('select[name=sbDealer]').val();
            if (data.moveTo == -1) {
                valid = false;

                alert('Выберите дилера для продолжения.');
            }
        }

        if (!valid) {
            return;
        }

        data.modelsIds = getCheckedModels();

        if (data.modelsIds.length == 0) {
            alert('Для продолжения необходимо выбрать заявк(у,и)');
        } else {
            $bt.fadeOut();
            $.post($(this).parent('form').attr('action'),
                data,
                function (result) {
                    var res = JSON.parse(result);

                    $bt.fadeIn();
                    if (res.success) {
                        $('.container-move-result').addClass('alert-info').html(res.msg).fadeIn();
                    }
                    else {
                        $('.container-move-result').addClass('alert-error').html(res.msg).fadeIn();
                    }
                });
        }
    });

    var getCheckedModels = function () {
        var result = [];

        $('.model-index').each(function (ind, el) {
            if ($(el).is(':checked')) {
                result.push($(el).data('model-id'));
            }
        });

        return result
    }
</script>