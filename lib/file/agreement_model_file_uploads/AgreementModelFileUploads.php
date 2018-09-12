        <?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 19.06.2017
 * Time: 17:50
 */
class AgreementModelFileUploads
{
    protected $_file_field = 'model_file';
    protected $_file_model = AgreementModel::UPLOADED_FILE_MODEL;
    protected $_file_model_type = AgreementModel::UPLOADED_FILE_MODEL_TYPE;

    protected $_user = null;
    protected $_uploaded_files_result = null;
    protected $_form = null;
    protected $_model = null;
    protected $_files_upload_count = 0;

    public function __construct(User $user, $uploaded_files_result, $model, $form, $files_upload_count = 0)
    {
        $this->_user = $user;
        $this->_uploaded_files_result = $uploaded_files_result;
        $this->_form = $form;
        $this->_model = $model;
        $this->_files_upload_count = $files_upload_count;
    }

    /**
     * Make file uploads
     * @param array $overloaded_fields
     */
    public function makeUpload($overloaded_fields = array()) {

        //Make override fields on class
        if (!empty($overloaded_fields) && is_array($overloaded_fields)) {
            $this->_file_model = $overloaded_fields['file_model'];
            $this->_file_model_type = $overloaded_fields['file_model_type'];
            $this->_file_field = $overloaded_fields['file_field'];
        }

        foreach ($this->_uploaded_files_result as $field_name => $file_data) {
            $file = $this->_form->getExtractedFile($field_name);

            //Make sure we only work with field what set in settings
            if (strpos($field_name, $this->_file_field) === FALSE) {
                continue;
            }

            if ($file) {
                if ($this->_files_upload_count == 1) {
                    $record = AgreementModelReportFilesTable::getInstance()->createQuery()->where('object_id = ? and object_type = ? and file_type = ? and field_name = ?',
                        array
                        (
                            $this->_model->getId(),
                            $this->_file_model,
                            $this->_file_model_type,
                            $field_name
                        )
                    )
                        ->fetchOne();

                    if ($record) {
                        $record->setFile($file);
                    } else {
                        $record = new AgreementModelReportFiles();
                        $record->setArray(
                            array(
                                'file' => $file,
                                'object_id' => $this->_model->getId(),
                                'object_type' => $this->_file_model,
                                'file_type' => $this->_file_model_type,
                                'user_id' => $this->_user->getId(),
                                'field' => $this->_file_field,
                                'field_name' => $field_name
                            )
                        );
                    }
                } else {
                    $record = new AgreementModelReportFiles();
                    $record->setArray(
                        array(
                            'file' => $file,
                            'object_id' => $this->_model->getId(),
                            'object_type' => $this->_file_model,
                            'file_type' => $this->_file_model_type,
                            'user_id' => $this->_user->getId(),
                            'field' => $this->_file_field,
                            'field_name' => $field_name
                        )
                    );
                }

                $record->save();
            }
        }

        $this->_model->reindexFiles();
    }
}
