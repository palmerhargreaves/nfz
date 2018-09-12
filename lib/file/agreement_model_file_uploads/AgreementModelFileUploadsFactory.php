<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 19.06.2017
 * Time: 17:47
 */

class AgreementModelFileUploadsFactory
{
    protected static $_instance = null;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new AgreementModelFileUploadsFactory();
        }

        return self::$_instance;
    }

    public function createUploadClass(User $user, $uploaded_files_result, $model, $form, $file_uploads_count = 0) {
        $cls_prefix = 'model';

        if ($model->isModelScenario()) {
            if ($model->getNoModelChanges()) {
                $cls_prefix = 'scenarioRecord';
            } else {
                $cls_prefix = $model->getStep1() == "accepted" ? 'record' : 'scenario';
            }
        }

        $cls = 'AgreementModelFileUploads'.ucfirst($cls_prefix);
        $uploader = new $cls($user, $uploaded_files_result, $model, $form, $file_uploads_count);

        return $uploader->makeUpload();
    }
}
