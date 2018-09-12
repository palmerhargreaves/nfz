<a href="javascript:;" class="on-add-limits-to-dialog" data-dialog-id="<?php echo $dialogs->getId(); ?>">Ограничения</a>
<?php
$info = $dialogs->getBindingPostsAndUsersInfo();

if($info['posts'] > 0) {
    echo sprintf('<br/><span style="font-size: 11px;">По правам: %s</span>', $info['posts']);
}

if($info['users'] > 0) {
    echo sprintf('<br/><span style="font-size: 11px;">По пользователям: %s</span>', $info['users']);
}
?>
