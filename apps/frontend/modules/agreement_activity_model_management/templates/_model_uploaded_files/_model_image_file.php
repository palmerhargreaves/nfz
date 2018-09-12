<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 12.10.2016
 * Time: 16:42
 */
$is_image = true;

foreach ($files_list as $file):
?>
<a href="<?php echo url_for('@agreement_model_download_file?file=' . $file) ?>"
   target="_blank"
   class="d-popup-uploaded-file <?php echo !$is_image ? 'odd ' . $file->getFileExt() : ''; ?>" <?php echo !$allow_remove ? "data-delete='false'" : ""; ?>>
    <?php if ($is_image): ?>
        <i><b><img src="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH . '/' . $file->getFileName(); ?>"/></b></i>
    <?php else: ?>
        <i></i>
    <?php endif; ?>
    <strong><?php echo $file->getFile() ?></strong>
    <em><?php echo $model->getModelFileNameHelperByFileName($file->getFileName())->getSmartSize(); ?></em>
    <?php if ($allow_remove && $model && ($model->getStatus() != "accepted" && $model->getStatus() != "wait")): ?>
        <span class="remove remove-uploaded-model-file"
              data-file-id="<?php echo $file->getId(); ?>"></span>
    <?php endif; ?>
</a>
<?php endforeach; ?>
