<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 03.06.2016
 * Time: 12:13
 */
?>

<?php foreach($messages as $message): ?>
    <div class="message<?php if($message->user_id == $sf_user->getAuthUser()->getId()) echo ' answer' ?>" data-message="<?php echo $message->getId() ?>">
        <div class="name online" data-user="<?php echo $message->getUserId() ?>"><div class="icon"></div><?php echo $message->getUserName() ?></div>
        <div class="time">
            <?php echo date('H:i', D::toUnix($message->created_at)) ?>
            <?php $date = D::toShortRus($message->created_at) ?>
            <?php if($date): ?>
                <span><?php echo $date ?></span>
            <?php endif; ?>
        </div>
        <div class="body"><div class="corner"></div><?php echo nl2br($message->getText()) ?>
            <?php if(isset($files[$message->getId()])): ?>
                <div class="attachments">
                    <?php foreach($files[$message->getId()] as $file): ?>
                        <?php if(!$file->getEditor()): ?>
                            <a href="<?php echo url_for("@agreement_model_discussion_message_download_file?file=".$file->getId()) ?>" target="_blank"><?php echo $file->getFile() ?> (<?php echo $file->getFileNameHelper()->getSmartSize() ?>)</a>
                        <?php else: ?>
                            <a href="<?php echo $file->getFile() ?>" target="_blank"><?php echo $file->getFile() ?> (<?php echo Utils::getRemoteFileSize($file->getFileName()) ?>)</a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else:
                $messageFiles = MessageFileTable::getInstance()->createQuery()->where('message_id = ?', $message->getId())->execute();
                foreach($messageFiles as $file):
                    ?>
                    <?php if(!$file->getEditor()): ?>
                    <a href="<?php echo url_for("@agreement_model_discussion_message_download_file?file=".$file->getId()) ?>" target="_blank"><?php echo $file->getFile() ?> (<?php echo $file->getFileNameHelper()->getSmartSize() ?>)</a>
                <?php else: ?>
                    <a href="<?php echo $file->getFile() ?>" target="_blank"><?php echo $file->getFile() ?> (<?php echo Utils::getRemoteFileSize($file->getFileName()) ?>)</a>
                <?php endif; ?>
                    <?php
                endforeach;
                ?>

            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
