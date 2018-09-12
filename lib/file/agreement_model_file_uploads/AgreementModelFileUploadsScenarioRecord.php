<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 19.06.2017
 * Time: 17:49
 */
class AgreementModelFileUploadsScenarioRecord extends AgreementModelFileUploads
{
    /**
     * Make uploads
     * @param array $overloaded_fields
     */
    public function makeUpload($overloaded_fields = array())
    {
        $fields = array(
            array(
                'file_field' => 'model_file',
                'file_model' => AgreementModel::UPLOADED_FILE_SCENARIO,
                'file_model_type' => AgreementModel::UPLOADED_FILE_SCENARIO_TYPE
            ),
            array(
                'file_field' => 'model_record_file',
                'file_model' => AgreementModel::UPLOADED_FILE_RECORD,
                'file_model_type' => AgreementModel::UPLOADED_FILE_RECORD_TYPE
            )
        );

        foreach ($fields as $key => $fields_data) {
            parent::makeUpload($fields_data);
        }
    }
}
