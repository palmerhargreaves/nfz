<?php

/**
 * TempFile form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TempFileForm extends BaseTempFileForm
{
    private $_upload_object_type = '';
    private $_upload_file_type = '';

    public function __construct($upload_object_type = '', $upload_file_type = '', $object = null, array $options = array(), $CSRFSecret = null)
    {
        $this->_upload_object_type = $upload_object_type;
        $this->_upload_file_type = $upload_file_type;

        parent::__construct($object, $options, $CSRFSecret);
    }

    public function configure()
    {
        unset($this['created_at'], $this['updated_at']);

        $this->widgetSchema['user_id'] = new sfWidgetFormInputHidden();
        $this->widgetSchema['activity_id'] = new sfWidgetFormInputHidden();

        $this->widgetSchema['file_object_type'] = new sfWidgetFormInputHidden();
        $this->widgetSchema['file_type'] = new sfWidgetFormInputHidden();
        $this->widgetSchema['file_size'] = new sfWidgetFormInputHidden();

        $this->widgetSchema['file'] = new sfWidgetFormInputFile(array(
            'label' => 'Файл',
        ));

        $max_upload_size = sfConfig::get('app_max_upload_size');

        $this->validatorSchema['file'] = new ValidatorFile(array(
            'required' => true,
            'max_size' => $max_upload_size,
            'path' => sfConfig::get('sf_upload_dir') . '/' . TempFile::FILE_PATH,
            'validated_file_class' => 'ValidatedFile',
            'mime_types' => $this->getMimeType(),
        ));

        $this->validatorSchema['file']->setMessage('mime_types', 'Запрещенный формат файла (%mime_type%) для данного типа заявок');
        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

    private function getMimeType()
    {
        if (empty($this->_upload_file_type)) {
            return array(
                'image/jpeg',
                'image/pjpeg',
                'image/gif',
                'image/png',
                'image/x-png',
                'text/csv',
                'text/plain',
                'application/csv',
                'application/x-csv',
                'text/comma-separated-values',
                'text/x-comma-separated-values',
                'text/tab-separated-values',
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
                'application/zip',
                'application/x-rar-compressed',
                'application/x-rar',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'image/tiff',
                'application/x-shockwave-flash',
                'audio/mpeg',
                'application/octet-stream',
                'video/x-ms-asf',
                'video/x-msvideo',
                'video/x-matroska',
                'video/quicktime',
                'audio/x-ms-wma',
                'video/mp4',
                'video/x-flv',
                'video/x-ms-wmv',
            );
        } else if ($this->_upload_file_type == 'model') {
            return array(
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/x-png',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/excel',
                'application/vnd.sealed.xls',
                'application/x-msexcel',
                'application/xexcel',
                'application/vnd.ms-office',
                'application/vnd.ms-excel.addin.macroEnabled.12',
                'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                'application/vnd.ms-excel.sheet.macroEnabled.12',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel.template.macroEnabled.12',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                'application/vnd.ms-powerpoint',
                'application/msexcel',
                'application/x-ms-excel',
                'application/x-excel',
                'application/x-dos_ms_excel',
                'application/xls',
                'application/x-xls',
                'application/x-shockwave-flash',
                'audio/mpeg',
                'audio/wav',
                'audio/x-wav',
                'application/octet-stream',
                'video/x-ms-asf',
                'video/x-msvideo',
                'video/x-matroska',
                'video/quicktime',
                'audio/x-ms-wma',
                'video/mp4',
                'video/x-flv',
                'video/x-ms-wmv',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/rtf',
                'text/plain'
            );
        } else if ($this->_upload_file_type == 'model_scenario') {
            return array(
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/excel',
                'application/vnd.sealed.xls',
                'application/x-msexcel',
                'application/xexcel',
                'application/vnd.ms-office',
                'application/vnd.ms-excel.addin.macroEnabled.12',
                'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                'application/vnd.ms-excel.sheet.macroEnabled.12',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel.template.macroEnabled.12',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                'application/vnd.ms-powerpoint',
                'application/msexcel',
                'application/x-ms-excel',
                'application/x-excel',
                'application/x-dos_ms_excel',
                'application/xls',
                'application/x-xls',
                'application/x-shockwave-flash',
                'application/rtf',
                'text/plain'
            );
        } else if ($this->_upload_file_type == 'model_record') {
            return array(
                'audio/mpeg',
                'audio/wav',
                'audio/x-wav',
                //'application/octet-stream',
                'video/x-ms-asf',
                'video/x-msvideo',
                'video/x-matroska',
                'video/quicktime',
                'audio/x-ms-wma',
                'video/mp4',
                'video/x-flv',
                'video/x-ms-wmv'
            );
        } else if ($this->_upload_file_type == 'report_additional' || $this->_upload_file_type == 'report_financial') {
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
}
