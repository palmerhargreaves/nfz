<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 17.10.2016
 * Time: 11:18
 */

class ModelReportFiles
{
    const UPLOADED_FILES_IMAGE = 'image';
    const UPLOADED_FILES_OTHERS = 'others';

    public static function getUploadedFilesListByObjectTypeAndFileType($object, $objectType, $file_type, $count = false)
    {
        $query = AgreementModelReportFilesTable::getInstance()
            ->createQuery()
            ->where('object_type = ? and object_id = ?',
                array
                (
                    $objectType,
                    $object->getId()
                )
            );

        if (is_array($file_type)) {
            $query->andWhereIn('file_type', $file_type);
        } else {
            $query->andWhere('file_type = ?', $file_type);
        }

        if ($count) {
            return $query->count();
        }

        return $query->execute();
    }

    public static function sortFileList(Closure $callback, $object, $uploaded_file_type, $uploaded_types, $f_type = null) {
        $result_all = array();

        $img_files_list_result = array();
        $files_list_result = array();

        $files_list = self::getUploadedFilesListByObjectTypeAndFileType($object, $uploaded_file_type, $uploaded_types);
        foreach ($files_list as $file) {
            if (!is_null($f_type)) {
                if ($file->isImage() && $f_type == self::UPLOADED_FILES_IMAGE) {
                    $result_all[] = $file;
                } else if (!$file->isImage() && $f_type == self::UPLOADED_FILES_OTHERS) {
                    $result_all[] = $file;
                }
            } else {
                if ($file->isImage()) {
                    $img_files_list_result[] = $file;
                } else if (!$file->isImage()) {
                    $files_list_result[] = $file;
                }
            }
        }

        $callback(!is_null($f_type) ? $result_all : array_merge($img_files_list_result, $files_list_result));
    }

    static function getModelFilesTypes() {
        return array(self::UPLOADED_FILES_IMAGE, self::UPLOADED_FILES_OTHERS);
    }

    public static function packUploadedFilesToZip($object, $by_type)
    {
        $gen_file = date('h:m:s', time()).'_'.$object->getId().'_'.date('d-m-Y', time()).'.zip';

        $zip = new ZipArchive();
        $zipFile = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR .$gen_file;

        @unlink($zipFile);
        $zip_handler = $zip->open($zipFile, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);

        $img_dir = array();
        $files_dir = arraY();

        $added_files = 0;
        if ($zip_handler) {
            $object_files_type = $object->getUploadedFilesSchemaByType();

            foreach (ModelReportFiles::getModelFilesTypes() as $f_type) {
                ModelReportFiles::sortFileList(function ($files_list) use ($object, $f_type, $img_dir, $files_dir, $zip, $by_type, &$added_files) {
                    foreach ($files_list as $file) {
                        $path = AgreementModel::MODEL_FILE_PATH;
                        if ($file->getFileType() == AgreementModelReport::UPLOADED_FILE_ADDITIONAL) {
                            $path = AgreementModelReport::ADDITIONAL_FILE_PATH;
                        } else if ($file->getFileType() == AgreementModelReport::UPLOADED_FILE_FINANCIAL) {
                            $path = AgreementModelReport::FINANCIAL_DOCS_FILE_PATH;
                        }

                        $file_path = sfConfig::get('app_uploads_path').'/' . $path . '/'.$file->getFileName();
                        if ($file->isImage()) {
                            $current_dir = 'img';
                            if (!in_array($current_dir, $img_dir)) {
                                $img_dir[] = $current_dir;

                                $zip->addEmptyDir($current_dir);
                            }
                        } else {
                            $current_dir = 'files';
                            if (!in_array($current_dir, $files_dir)) {
                                $files_dir[] = $current_dir;

                                $zip->addEmptyDir($current_dir);
                            }
                        }

                        $info = pathinfo($file->getFile());
                        $fileInfo = sprintf('[%s] %s.%s', $object->getId(), $info['filename'], $info['extension']);

                        $added_files++;

                        $zip->addFile($file_path, $current_dir . '/' . $fileInfo);
                    }
                },
                    $object,
                    $object_files_type[$by_type]['type'],
                    $by_type,
                    $f_type
                );
            }
        }
        $zip->close();

        return '/uploads/'.$gen_file;
    }
}
