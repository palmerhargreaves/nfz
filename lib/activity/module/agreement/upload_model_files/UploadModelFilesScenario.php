<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 17.05.2017
 * Time: 12:08
 */

class UploadModelFilesScenario extends UploadModelFiles
{
    protected function getAlreadyUploadedFiles() {
        $this->FILE_MODEL = AgreementModel::UPLOADED_FILE_SCENARIO;
        $this->FILE_MODEL_TYPE = AgreementModel::UPLOADED_FILE_SCENARIO_TYPE;

        return $this->_model->getModelUploadedScenarioRecordFiles(AgreementModel::BY_SCENARIO);
    }
}
