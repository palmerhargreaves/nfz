<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 15.10.2016
 * Time: 13:30
 */

$total_files_size = 0;
$total_files = 0;

if ($report) {
    $report->getSortedUploadedFilesList(function ($uploaded_files) use ($by_type, &$total_files_size, &$total_files, $report) {
        $total_files = count($uploaded_files);

        foreach ($uploaded_files as $file):
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
            <span class="d-popup-uploaded-file <?php echo !$is_image ? 'odd ' . $file->getFileExt() : ''; ?>"
                  data-delete="false">
                            <?php if ($is_image): ?>
                                <i><b><img src="/uploads/<?php echo $img_path; ?>"/></b></i>
                            <?php else: ?>
                                <i></i>
                            <?php endif; ?>

                <strong><a
                            href="<?php echo url_for('@agreement_report_download_file?file=' . $file . '&by_type=' . $by_type) ?>"
                            target="_blank"><?php echo $file->getFile() ?></a></strong>
                             <em><?php echo $smart_file_size; ?></em>

                <?php if ($report && ($report->getStatus() != "accepted" && $report->getStatus() != "wait")): ?>
                    <span class="remove remove-uploaded-report-file " data-file-id="<?php echo $file->getId(); ?>"
                          data-by-type="<?php echo $by_type; ?>"
                          data-report-id="<?php echo $report->getId(); ?>"
                          data-is-concept="<?php echo $report->getModel()->isConcept() ? 1 : 0; ?>"
                    ></span>
                <?php endif; ?>
            </span>

        <?php endforeach; ?>
    <?php }, $by_type); ?>

    <div id="report_files_caption_<?php echo $by_type; ?>_temp" class="caption">Прикреплено: <?php echo $total_files; ?>
        .<br/> Общий размер: <?php echo F::getSmartSize($total_files_size); ?></div>

<?php } ?>
