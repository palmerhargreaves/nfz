<?php

$total_files_size = 0;
$total_files = 0;
if ($model):

    $files_types = array();
    if ($model->isModelScenario()) {
        $files_types[] = AgreementModel::UPLOADED_FILE_SCENARIO_TYPE;
    } else {
        $files_types[] = AgreementModel::UPLOADED_FILE_MODEL_TYPE;
    }

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

                <?php if (!$file->getIsExternalFile()): ?>
                    <?php if ($model->isModelScenario()): ?>
                        <?php if ($model && ($model->getStatus() != "accepted" && $model->getStatus() != "wait" && $model->getStatus() != "wait_manager_specialist" && $model->getStep1() != 'accepted')): ?>
                            <span class="remove remove-uploaded-model-file"
                                  data-file-id="<?php echo $file->getId(); ?>"></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($model && ($model->getStatus() != "accepted" && $model->getStatus() != "wait" && $model->getStatus() != "wait_manager_specialist")): ?>
                            <span class="remove remove-uploaded-model-file"
                                  data-file-id="<?php echo $file->getId(); ?>"></span>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
        </span>
            <?php $total_files_size += $model->getModelFileNameHelperByFileName($file->getFileName())->getSize();
        endforeach;
    },
        $model,
        AgreementModel::UPLOADED_FILE_MODEL,
        $files_types
    );

endif;
?>

<div id="model_files_caption_temp" class="caption">Прикреплено: <?php echo $total_files; ?>. Общий
    размер: <?php echo F::getSmartSize($total_files_size); ?></div>
