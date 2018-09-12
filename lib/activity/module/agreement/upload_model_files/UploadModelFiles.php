<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 17.05.2017
 * Time: 11:50
 */

class UploadModelFiles {
    protected $FILE_MODEL = AgreementModel::UPLOADED_FILE_MODEL;
    protected $FILE_MODEL_TYPE = AgreementModel::UPLOADED_FILE_MODEL_TYPE;

    protected $_model = null;
    protected $_upload_files_ids = "";
    protected $_user = null;
    protected $_activity_id = 0;

    public function __construct(AgreementModel $model, $user, $upload_files_ids, $activity_id = 0)
    {
        $this->_model = $model;
        $this->_user = $user;
        $this->_upload_files_ids = $upload_files_ids;
        $this->_activity_id = $activity_id;
    }

    public function saveFiles() {
        /**
         * Make check what model type and then get all uploaded files by model type
         */
        $upload_files_ids = $this->_upload_files_ids;
        if (is_null($upload_files_ids)) {
            $upload_files_ids = array();
        }

        $model_uploaded_files = array();

        if ($this->_user->getAttribute('editor_link')) {
            $editor_file_name = F::copyExternalFileTo($this->_user->getAttribute('editor_link'), sfConfig::get('sf_upload_dir') . '/' . TempFile::FILE_PATH);
            $temp_file = new TempFile();
            $temp_file->setArray(
                array
                (
                    'file' => $editor_file_name,
                    'file_object_type' => $this->FILE_MODEL,
                    'file_type' => $this->FILE_MODEL_TYPE,
                    'user_id' => $this->_user->getAuthUser()->getId(),
                    'is_external_file' => true
                )
            );
            $temp_file->save();

            $upload_files_ids[] = $temp_file->getId();
        } else {
            $model_uploaded_files = $this->getAlreadyUploadedFiles();
        }

        /**
         * If model type was changed then change uploaded model files to this type
         */
        foreach ($model_uploaded_files as $file) {
            $file->setFileType($this->FILE_MODEL_TYPE);
            $file->save();
        }

        //Если загружены новые файлы на сервер, добавляем их в БД
        if (!is_array($upload_files_ids)) {
            $upload_files_ids = explode(":", $upload_files_ids);
        }

        $temp_files_list_query = TempFileTable::getInstance()->createQuery()
            ->whereIn('id', $upload_files_ids);
            //->andWhere('file_object_type = ? and file_type = ?', array($this->FILE_MODEL, $this->FILE_MODEL_TYPE));

        if ($this->_activity_id != 0) {
            //$temp_files_list_query->andWhere('activity_id = ?', $this->_activity_id);
        }

        $temp_files_list = $temp_files_list_query->execute();

        $copied_files = array();
        foreach ($temp_files_list as $temp_file) {
            $copy_file = TempFileTable::copyFileTo($temp_file, AgreementModel::MODEL_FILE_PATH, $this->_user->getAuthUser());

            $copied_files[] = $copy_file;
            $record = new AgreementModelReportFiles();
            $record->setArray(
                array(
                    'file' => $copy_file['gen_file_name'],
                    'object_id' => $this->_model->getId(),
                    'activity_id' => $this->_activity_id,
                    'object_type' => $this->FILE_MODEL,
                    'file_type' => $this->FILE_MODEL_TYPE,
                    'user_id' => $this->_user->getAuthUser()->getId(),
                    'field' => '',
                    'field_name' => '',
                    'is_external_file' => $temp_file->getIsExternalFile(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'path' => $copy_file['upload_path']
                )
            );
            $record->save();

            TempFileTable::removeFile($temp_file);
        }

        if (!empty($model_uploaded_files)) {
            foreach ($model_uploaded_files as $model_file) {
                $copied_files[] = array('gen_file_name' => $model_file->getFile(), 'upload_path' => $model_file->getPath());
            }
        }

        return $copied_files;
    }

    public function reuploadFiles() {
        $upload_files_ids = $this->_upload_files_ids;

        $model_uploaded_files = $this->getAlreadyUploadedFiles();

        /**
         * If model type was changed then change uploaded model files to this type
         */
        foreach ($model_uploaded_files as $file) {
            $file->setFileType($this->FILE_MODEL_TYPE);
            $file->save();
        }

        //Если загружены новые файлы на сервер, добавляем их в БД
        if (!is_array($upload_files_ids)) {
            $upload_files_ids = explode(":", $upload_files_ids);
        }

        $temp_files_list_query = TempFileTable::getInstance()->createQuery()
            ->whereIn('id', $upload_files_ids);
        //->andWhere('file_object_type = ? and file_type = ?', array($this->FILE_MODEL, $this->FILE_MODEL_TYPE));

        $temp_files_list = $temp_files_list_query->execute();

        $auth_user = $this->_user;

        $copied_files = array();
        foreach ($temp_files_list as $temp_file) {
            $copy_file = TempFileTable::copyFileTo($temp_file, AgreementModel::MODEL_FILE_PATH, $auth_user);

            $copied_files[] = $copy_file;
            $record = new AgreementModelReportFiles();
            $record->setArray(
                array(
                    'file' => $copy_file['gen_file_name'],
                    'object_id' => $this->_model->getId(),
                    'activity_id' => $this->_activity_id,
                    'object_type' => $this->FILE_MODEL,
                    'file_type' => $this->FILE_MODEL_TYPE,
                    'user_id' => $auth_user->getId(),
                    'field' => '',
                    'field_name' => '',
                    'is_external_file' => $temp_file->getIsExternalFile(),
                    //'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'path' => $copy_file['upload_path']
                )
            );
            $record->save();

            TempFileTable::removeFile($temp_file);
        }

        if (!empty($model_uploaded_files)) {
            foreach ($model_uploaded_files as $model_file) {
                $copied_files[] = array('gen_file_name' => $model_file->getFile(), 'upload_path' => $model_file->getPath());
            }
        }

        return $copied_files;
    }

    protected function getAlreadyUploadedFiles() {
        return $this->_model->getModelUploadedFiles();
    }
}
