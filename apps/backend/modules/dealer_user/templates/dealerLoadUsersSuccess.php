<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 25.12.2015
 * Time: 3:44
 */
?>
<option value="-1">Выберите пользователя ...</option>
<?php
foreach($users as $user): ?>
    <option value="<?php echo $user->getId(); ?>"><?php echo sprintf('[%s] %s', $user->getSurname(),$user->getEmail()); ?></option>
<?php endforeach; ?>
