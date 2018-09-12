<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 17.05.2017
 * Time: 11:58
 */

class UploadModelFilesFactory {
    private static $_instance = null;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new UploadModelFilesFactory();
        }

        return self::$_instance;
    }

    public function createUpload(AgreementModel $model, $user, $upload_files_ids, $activity_id, $cls_prefix = '') {
        if (empty($cls_prefix)) {
            return new UploadModelFiles($model, $user, $upload_files_ids, $activity_id);
        }

        $cls = 'UploadModelFiles'.$cls_prefix;

        return new $cls($model, $user, $upload_files_ids, $activity_id);
    }
}
