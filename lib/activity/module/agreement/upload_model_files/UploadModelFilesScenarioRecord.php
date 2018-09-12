<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 17.05.2017
 * Time: 12:08
 */

class UploadModelFilesScenarioRecord extends UploadModelFiles {

    public function getAlreadyUploadedFiles() {
        if ($this->_model->getStep1() == "accepted") {
            $this->FILE_MODEL = AgreementModel::UPLOADED_FILE_RECORD;
            $this->FILE_MODEL_TYPE = AgreementModel::UPLOADED_FILE_RECORD_TYPE;

            return $this->_model->getModelUploadedScenarioRecordFiles(AgreementModel::BY_RECORD);
        } else {
            $this->FILE_MODEL = AgreementModel::UPLOADED_FILE_SCENARIO;
            $this->FILE_MODEL_TYPE = AgreementModel::UPLOADED_FILE_SCENARIO_TYPE;

            return $this->_model->getModelUploadedScenarioRecordFiles(AgreementModel::BY_SCENARIO);
        }

        return array();
    }
}


