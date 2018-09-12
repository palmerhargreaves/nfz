<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 22.09.2016
 * Time: 12:34
 */
?>

<div class="modal hide fade full-info-modal" id="user-bind-dealers-modal" style="width: 550px; left: 47%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Привязка дилеров</h3>
    </div>
    <div class="modal-body">
        <div class="panel-info-left fields-list" style="width: 35%; float:left;"></div>
        <div style="float:right; width: 62%; text-align: center;">
            <div class='inputs-fields panel-info-content'></div>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {
        window.users_binded_dealers = new BindDealers({
            modal: '#user-bind-dealers-modal',
            on_delete_binded_dealer_url: '<?php echo url_for("user_unbind_binded_dealer"); ?>',
            on_add_binded_dealer_url: '<?php echo url_for("user_bind_dealers"); ?>',
            on_load_user_binded_data: '<?php echo url_for("user_load_binded_dealers"); ?>',
            on_reload_user_binded_dealers_row: '<?php echo url_for("user_binded_dealers_reload_row"); ?>'
        }).start();
    });
</script>

