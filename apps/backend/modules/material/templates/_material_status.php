<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 18.04.2017
 * Time: 11:34
 */

?>

<input type="checkbox" name="material_status_<?php echo $material->getId(); ?>" <?php echo $material->getMaterialStatus() ? "checked" : ""; ?>
        class="material-status"
        data-url="<?php echo url_for('@material_change_status'); ?>"
        data-material-id="<?php echo $material->getId(); ?>"
        data-status="<?php echo $material->getMaterialStatus() ? true : false; ?>"
/>
