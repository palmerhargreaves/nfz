<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <form>
                    <ul class="nav nav-list">
                        <li class="nav-header">Параметры</li>
                        <li>
                            Статус выполнения:<br/>
                            <select id="sbActivityStatus">
                                <option value="0" <?php echo !$setting->getComplete() ? 'selected' : ''; ?>>Активна</option>
                                <option value="1" <?php echo $setting->getComplete() ? 'selected' : ''; ?>>Выполнена</option>
                            </select>
                        </li>
                        <li>
                            Квартал: <br/>
                            <select id="sbQuarter">
                                <option value="-1">Все кварталы</option>
                                <?php
                                    for($i = 1; $i <= 4; $i++):
                                        $qFunction = 'getQ'.$i;
                                ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i.sprintf('(%s)', $setting->$qFunction() != 0 ? 'Выполнен' : 'Активен'); ?></option>
                                <?php endfor; ?>
                            </select>
                        </li>
                        <li>
                            Разрешить доступ (всегда открыта):<br/>
                            <div class="checkbox">
                                <label><input type="checkbox" id="chAlwaysOpen" name="chAlwaysOpen" <?php echo $setting->getAlwaysOpen() ? "checked" : ""; ?> />Открыта</label>
                            </div>
                        </li>
                        <li>
                            <input type="button" id="btDoApplySettingsData" class="btn" style="margin-top: 15px;" value="Принять" />
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>
