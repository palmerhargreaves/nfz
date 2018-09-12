<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 22.09.2016
 * Time: 12:52
 */
?>
<div class="row">
    <form class="form-horizontal">
        <?php if ($binded_dealers): ?>
            <div class="span6 alert alert-info">
                Привязанные дилер(ы):
                <table class="table table-stripped" style="margin-top: 7px;">
                    <thead>
                    <tr>
                        <td>Дилер</td>
                        <td>Действие</td>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($binded_dealers as $bind_dealer): ?>
                        <tr class="row-binded-dealer-<?php echo $bind_dealer->getDealerId(); ?>">
                            <td><?php echo sprintf('%s (%s)', $bind_dealer->getDealer()->getNameAndNumber(), $bind_dealer->getDealer()->getDealerTypeLabel()); ?></td>
                            <td>
                                <button class="btn btn-mini btn-warning bt-unbind-dealer-from-user"
                                        data-dealer-id="<?php echo $bind_dealer->getDealerId(); ?>"
                                        data-user-id="<?php echo $bind_dealer->getUserId(); ?>">Отвязать</button>
                            </td>
                        </tr
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="span6">
            <div class="control-group">
                <label class="control-label" style="width: 10em;" for="sb_user_dealer_to_bind">Привязать к
                    дилеру:</label>

                <div class="controls">
                    <select id="sb_user_dealer_to_bind">
                    <?php foreach ($dealers_list as $dealer): ?>
                        <option value="<?php echo $dealer->getId(); ?>"><?php echo sprintf('%s (%s)', $dealer->getNameAndNumber(), $dealer->getDealerTypeLabel()); ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="span6">
            <div class="control-group">
                <button id="bt-bind-selected-dealer-to-user" class="btn btn-success">Привязать</button>
            </div>
        </div>

    </form>
</div>