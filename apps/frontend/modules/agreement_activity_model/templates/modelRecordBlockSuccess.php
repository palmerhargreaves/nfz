<?php

$total_files_size = 0;
$total_files = 0;
if ($model):
    ModelReportFiles::sortFileList(function ($uploaded_files_list) use (&$total_files, &$total_files_size, $model) {
        $total_files = count($uploaded_files_list);
        foreach ($uploaded_files_list as $file):
            $is_image = $file->isImage();

            ?>
            <span class="d-popup-uploaded-file <?php echo !$is_image ? 'odd ' . $file->getFileExt() : ''; ?>"
                  data-delete="false">
                        <?php if ($is_image): ?>
                            <i><b><img src="/uploads/<?php echo AgreementModel::MODEL_FILE_PATH . '/' . $file->getFileName(); ?>"/></b></i>
                        <?php else: ?>
                            <i></i>
                        <?php endif; ?>

                <strong><a href="<?php echo url_for('@agreement_model_download_file?file=' . $file) ?>"
                           target="_blank"><?php echo $file->getFile() ?></a></strong>
                         <em><?php echo $model->getModelFileNameHelperByFileName($file->getFileName())->getSmartSize(); ?></em>

                <?php if ($model && ($model->getStatus() != "accepted" && $model->getStatus() != "wait_manager_specialist" && $model->getStatus() != "wait" && $model->getStep2() != 'accepted')): ?>
                    <span class="remove remove-uploaded-model-record-file"
                          data-file-id="<?php echo $file->getId(); ?>"></span>
                <?php endif; ?>
        </span>
            <?php $total_files_size += $model->getModelFileNameHelperByFileName($file->getFileName())->getSize();
        endforeach;
    },
        $model,
        AgreementModel::UPLOADED_FILE_MODEL,
        array
        (
            AgreementModel::UPLOADED_FILE_RECORD_TYPE,
        )
    );

else:
    ?>

<?php endif; ?>

<div id="model_record_files_caption_temp" class="caption">Прикреплено: <?php echo $total_files; ?>. Общий
    размер: <?php echo F::getSmartSize($total_files_size); ?></div>
