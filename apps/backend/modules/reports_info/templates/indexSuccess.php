<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Выгрузка отчетов</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <?php if ($status): ?>
                <div class="alert alert-success container-success" style="">
                    Данные по успешно выгружены
                </div>
                <?php endif; ?>

                <form action="<?php echo url_for('reports_info/index') ?>" method="post" class="form-inline"
                      id="reports-form">
                    <ul class="nav nav-list">
                        <li class="nav-header">Фильтр:</li>
                        <li>
                            Дилер:<br/>
                            <select id='dealer_filter' name='dealer_filter'>
                                <option value='-1'>Все дилеры ...</option>
                                <?php foreach ($dealers as $dealer): ?>
                                    <?php
                                    $sel = '';
                                    if ($dealer_filter && $dealer_filter == $dealer->getId()) $sel = 'selected';
                                    ?>
                                    <option
                                        value="<?php echo $dealer->getId() ?>" <?php echo $sel; ?>><?php echo $dealer->getRawValue() ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                        <li>
                            Активность:<br/>
                            <select id='activity_filter' name='activity_filter'>
                                <option value='-1'>Все активности ...</option>
                                <?php foreach ($activities as $activity): ?>
                                    <?php
                                    $sel = '';
                                    if ($activity_filter && $activity_filter == $activity->getId()) $sel = 'selected';
                                    ?>
                                    <option
                                        value="<?php echo $activity->getId() ?>" <?php echo $sel; ?>><?php echo sprintf('%s - %s', $activity->getId(), $activity->getName()); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>

                        <li>
                            От:<br/>
                            <input type="text" name="start_date" placeholder="от"
                                   value="<?php echo isset($start_date) ? $start_date : '' ?>"
                                   class="input-small date">
                        </li>

                        <li>
                            До:<br/>
                            <input type="text" name="end_date" placeholder="до"
                                   value="<?php echo isset($end_date) ? $end_date : '' ?>"
                                   class="input-small date">
                        </li>

                        <li>
                            Только концепции:<br/>
                            <input type="checkbox" name="ch_only_concepts" value="1" <?php echo !is_null($onlyConcepts) ? "checked" : ""; ?> class="input-small">
                        </li>

                        <li>
                            <input type="submit" id="btDoFilterData" class="btn" style="margin-top: 15px;"
                                   value="Выгрузить"/>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#reports-form input.date').datepicker({dateFormat: "dd.mm.y"});
</script>