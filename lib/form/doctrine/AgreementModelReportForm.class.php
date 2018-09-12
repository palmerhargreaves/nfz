<?php

/**
 * AgreementModelReport form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AgreementModelReportForm extends BaseAgreementModelReportForm
{
    private $mime_types;
    const MAX_FILES = 10;

    private $_extracted_fields = array();
    private $_extracted_files = array();

    public function configure()
    {
        unset($this['created_at'], $this['updated_at'], $this['agreement_comments'], $this['agreement_comments_file']);

        /*$this->widgetSchema['financial_docs_file'] = new sfWidgetFormInputFile(array(
          'label' => 'Финансовые документы (счет, акт, платежное поручение)',
        ));*/

        $this->widgetSchema['cost'] = new sfWidgetFormInputText();

        for ($i = 1; $i <= sfConfig::get('app_max_files_upload_count'); $i++)
        {
            $this->widgetSchema['additional_file_' . $i] = new sfWidgetFormInputText();
            $this->widgetSchema['additional_file_ext_' . $i] = new sfWidgetFormInputText();
            $this->widgetSchema['financial_docs_file_' . $i] = new sfWidgetFormInputText();

            $this->_extracted_fields[] = 'additional_file_' . $i;
            $this->_extracted_fields[] = 'additional_file_ext_' . $i;
            $this->_extracted_fields[] = 'financial_docs_file_' . $i;

            $this->validatorSchema['additional_file_' . $i] = new sfValidatorString(array('max_length' => 255, 'required' => false));
            $this->validatorSchema['additional_file_ext_' . $i] = new sfValidatorString(array('max_length' => 255, 'required' => false));
            $this->validatorSchema['financial_docs_file_' . $i] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        }

        $this->_extracted_fields[] = 'additional_file';
        $this->_extracted_fields[] = 'financial_docs_file';

        //Additional files
        $this->addFormField('additional_file', AgreementModelReport::ADDITIONAL_FILE_PATH);

        //Financial
        $this->addFormField('financial_docs_file', AgreementModelReport::FINANCIAL_DOCS_FILE_PATH);

        $add_req = true;
        $fin_req = true;

        if ($this->getObject()->getId()) {
            $model = $this->getObject()->getModel();

            $uploaded_files_count = $model->getReportUploadedFilesCount();
            if (isset($uploaded_files_count[AgreementModel::UPLOADED_FILE_FINANCIAL_FILE_TYPE]) && $uploaded_files_count[AgreementModel::UPLOADED_FILE_FINANCIAL_FILE_TYPE] > 0) {
                $fin_req = false;
            }

            if (isset($uploaded_files_count[AgreementModel::UPLOADED_FILE_ADDITIONAL_FILE_TYPE]) && $uploaded_files_count[AgreementModel::UPLOADED_FILE_ADDITIONAL_FILE_TYPE] > 0) {
                $add_req = false;
            }
        }

        //Additional / Financial files
        for ($i = 1; $i <= sfConfig::get('app_max_files_upload_count'); $i++) {
            $this->addFormField('additional_file_' . $i, AgreementModelReport::ADDITIONAL_FILE_PATH, $this->getMimeTypes(), sfConfig::get(''), $i == 1 && $add_req ? true : false);
            $this->addFormField('additional_file_ext_' . $i, AgreementModelReport::ADDITIONAL_FILE_PATH, $this->getMimeTypes(), sfConfig::get(''));

            $this->addFormField('financial_docs_file_' . $i, AgreementModelReport::FINANCIAL_DOCS_FILE_PATH, $this->getMimeTypes(), sfConfig::get(''), $i == 1 && $fin_req ? true : false);
        }

        $this->validatorSchema['cost'] = new sfValidatorNumber(array('required' => false));
        $this->validatorSchema['cost']->setMessage('invalid', '"%value%" не является числом');

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

    protected function processUploadedFile($field, $filename = null, $values = null)
    {
        if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
        {
            throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
        }

        if (null === $values)
        {
            $values = $this->values;
        }

        if (isset($values[$field.'_delete']) && $values[$field.'_delete'])
        {
            $this->removeFile($field);
            return '';
        }

        if (!$values[$field])
        {
            // this is needed if the form is embedded, in which case
            // the parent form has already changed the value of the field
            $oldValues = $this->getObject()->getModified(true, false);

            if (in_array($field, $this->_extracted_fields)) {
                return null;
            }

            return isset($oldValues[$field]) ? $oldValues[$field] : $this->object->$field;
        }

        // we need the base directory
        if (!$this->validatorSchema[$field]->getOption('path')) {
            return $values[$field];
        }

        $this->removeFile($field);

        return $this->saveFile($field, $filename, $values[$field]);
    }

    public function doUpdateObject($values) {
        foreach($this->_extracted_fields as $field) {
            if (isset($values[$field])) {
                $this->_extracted_files[$field] = $values[$field];
                unset($values[$field]);
            }
        }

        parent::doUpdateObject($values);
    }

    /**
     * Removes the current file for the field.
     *
     * @param string $field The field name
     */
    protected function removeFile($field)
    {
        if (!$this->validatorSchema[$field] instanceof sfValidatorFile) {
            throw new LogicException(sprintf('You cannot remove the current file for field "%s" as the field is not a file.', $field));
        }

        $directory = $this->validatorSchema[$field]->getOption('path');

        if (!in_array($field, $this->_extracted_fields)) {
            if ($directory && is_file($file = $directory . '/' . $this->getObject()->$field)) {
                unlink($file);
            }
        }
    }

    public function getExtractedFile($field)
    {
        if (isset($this->_extracted_files[$field])) {
            return $this->_extracted_files[$field];
        }

        return null;
    }

    private function addFormField($field, $path = '', $mimeTypes = null, $max_file_size = null, $req = false)
    {
        $this->widgetSchema[$field] = new sfWidgetFormInputFile(array(
            'label' => 'Файл',
        ));

        $this->validatorSchema[$field] = new sfValidatorFile(array(
            'required' => $req,
            'max_size' => $max_file_size,
            'path' => sfConfig::get('sf_upload_dir') . '/' . $path,
            'validated_file_class' => 'ValidatedFile',
            'mime_types' => is_null($mimeTypes) ? $this->getMimeTypes() : $mimeTypes
        ));

        $this->validatorSchema[$field]->addMessage('mime_types', 'Формат файла не поддерживается %mime_type%');
        $this->validatorSchema[$field]->addMessage('max_size', 'Файл слишком большой (максимум - это %max_size% байт)');
    }

    private function getMimeTypes() {
        return array(
            'image/jpeg',
            'image/pjpeg',
            'image/gif',
            'image/png',
            'image/x-png',
            'application/pdf',
            'application/postscript',
            'image/vnd.adobe.photoshop',
            'application/cdr',
            'application/coreldraw',
            'application/x-cdr',
            'application/x-coreldraw',
            'image/cdr',
            'image/x-cdr',
            'zz-application/zz-winassoc-cdr',
            'application/msword',
            'application/vnd.ms-office',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'image/tiff',
            'audio/mpeg',
            'application/octet-stream',
            'video/x-ms-asf',
            'application/x-shockwave-flash',
            'audio/mpeg',
            'audio/wav',
            'audio/x-wav',
            'video/x-ms-asf',
            'video/x-msvideo',
            'video/x-matroska',
            'video/quicktime',
            'audio/x-ms-wma',
            'video/mp4',
            'video/x-flv',
            'video/x-ms-wmv'
        );
    }
}
