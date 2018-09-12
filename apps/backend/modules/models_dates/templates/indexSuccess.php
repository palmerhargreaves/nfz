<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Перенос заявок</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <div class="alert alert-success">
                    Для поиска нескольких заявок необходимо добавить `,` между номерами заявок.
                </div>

                <form action="<?php echo url_for('find_model'); ?>">
                    <ul class="nav nav-list">
                        <li class="nav-header">Поиск заявок</li>
                        <li>
                            Номер заяв(ки, ок):<br/>
                            <textarea type="text" name="model_id" placeholder="Номер заяв(ки, ок)" cols="80" rows="4" value="" class="input" style="width: 297px;"></textarea>
                        </li>
                        <li>
                            Перенести в:
                            <select name="sbMoveType">
                                <option value="activity">Активность / Дата</option>
                                <option value="dealer">Дилер</option>
                            </select>
                        </li>
                        <li>
                            <input type="submit" id="btDoFilterData" class="btn" style="margin-top: 15px;" value="Поиск" />
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>