<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 17.05.2017
 * Time: 12:08
 */

class UploadModelFilesRecord extends UploadModelFiles
{
    protected function getAlreadyUploadedFiles() {
        $this->FILE_MODEL = AgreementModel::UPLOADED_FILE_RECORD;
        $this->FILE_MODEL_TYPE = AgreementModel::UPLOADED_FILE_RECORD_TYPE;

        return $this->_model->getModelUploadedScenarioRecordFiles(AgreementModel::BY_RECORD);
    }
}
