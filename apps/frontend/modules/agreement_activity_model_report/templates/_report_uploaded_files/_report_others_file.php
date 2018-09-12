<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 12.10.2016
 * Time: 16:42
 */
$is_image = false;
$total_files_size = 0;

foreach ($files_list as $file):
    $is_image = $file->isImage();

    if ($by_type == AgreementModelReport::UPLOADED_FILE_ADDITIONAL) {
        $total_files_size += $report->getAdditionalFileNameHelperByName($file->getFileName())->getSize();
        $smart_file_size = $report->getAdditionalFileNameHelperByName($file->getFileName())->getSmartSize();
        $img_path = AgreementModelReport::ADDITIONAL_FILE_PATH . '/' . $file->getFileName();
    } else {
        $total_files_size += $report->getFinancialDocsFileNameHelperByName($file->getFileName())->getSize();
        $smart_file_size = $report->getFinancialDocsFileNameHelperByName($file->getFileName())->getSmartSize();
        $img_path = AgreementModelReport::FINANCIAL_DOCS_FILE_PATH . '/' . $file->getFileName();
    }

    ?>

    <a href="<?php echo url_for('@agreement_model_download_file?file=' . $file->getId()) ?>"
       target="_blank"
       class="d-popup-uploaded-file <?php echo $report->inFavoritesFile($file->getId(), $file->getFile()) ? "hvr-curl-top-right-no-hover" : ""; ?>
    <?php echo isset($allow_add_file_to_favorites) && $allow_add_file_to_favorites && $model_type->getIsPhotoReport() && ($sf_user->isAdmin() || $sf_user->isManager() || $sf_user->isSpecialist()) ? "hvr-curl-top-right" : ""; ?> <?php echo !$is_image ? 'odd ' . $file->getFileExt() : ''; ?>" <?php echo !$allow_remove ? "data-delete='false'" : ""; ?>>
        <?php if ($is_image): ?>
            <i><b><img src="/uploads/<?php echo ($file->getFileType() == AgreementModelReport::UPLOADED_FILE_FINANCIAL
                            ? AgreementModelReport::FINANCIAL_DOCS_FILE_PATH
                            : AgreementModelReport::ADDITIONAL_FILE_PATH ) . '/' . $file->getFileName()  ; ?>"/></b></i>
        <?php else: ?>
            <i></i>
        <?php endif; ?>
        <strong><?php echo $file->getFile() ?></strong>
        <em><?php echo ($file->getFileType() == AgreementModelReport::UPLOADED_FILE_FINANCIAL
                ? $report->getFinancialDocsFileNameHelperByName($file->getFileName())->getSmartSize()
                : $report->getAdditionalFileNameHelperByName($file->getFileName())->getSmartSize()); ?></em>
        <?php if ($allow_remove && $model && ($model->getStatus() != "accepted" && $model->getStatus() != "wait")): ?>
            <span class="remove remove-uploaded-model-file"
                  data-file-id="<?php echo $file->getId(); ?>"></span>
        <?php endif; ?>

        <?php if (isset($allow_add_file_to_favorites) && $allow_add_file_to_favorites && $model_type->getIsPhotoReport() && ($sf_user->isAdmin() || $sf_user->isManager() || $sf_user->isSpecialist())): ?>
            <div class="favs-actions-container-<?php echo $file->getId(); ?>">
                <?php if ($report->inFavoritesFile($file->getId(), $file->getFile())): ?>
                    <?php include_partial('agreement_activity_model_management/favs/_remove_favSuccess', array('file' => $file, 'report' => $report, 'model_type_id' => $model_type->getId())); ?>
                <?php else: ?>
                    <?php include_partial('agreement_activity_model_management/favs/_add_to_favSuccess', array('file' => $file, 'report' => $report, 'model_type_id' => $model_type->getId())); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </a>
<?php endforeach; ?>
