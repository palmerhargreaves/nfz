<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.08.2016
 * Time: 11:07
 */

foreach ($values as $key => $value): ?>
    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
<?php endforeach; ?>

