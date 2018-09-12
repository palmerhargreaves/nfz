<?php

/**
 * AgreementModelReport form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AgreementModelReportFormN extends BaseAgreementModelReportForm
{
    private $mime_types;

    public function configure()
    {
        unset($this['created_at'], $this['updated_at'], $this['agreement_comments'], $this['agreement_comments_file']);

        $this->widgetSchema['is_valid_add_data'] = new sfWidgetFormInputHidden();
        $this->widgetSchema['is_valid_fin_data'] = new sfWidgetFormInputHidden();

        /*$this->widgetSchema['financial_docs_file'] = new sfWidgetFormInputFile(array(
          'label' => 'Финансовые документы (счет, акт, платежное поручение)',
        ));*/

        $this->mime_types = array(
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

        $this->widgetSchema['cost'] = new sfWidgetFormInputText();

        /*$this->validatorSchema['financial_docs_file'] = new sfValidatorFile(array(
          'required'   => false,
          'max_size' => sfConfig::get('app_max_upload_size'),
          'path'       => sfConfig::get('sf_upload_dir').'/'. AgreementModelReport::FINANCIAL_DOCS_FILE_PATH,
          'validated_file_class' => 'ValidatedFile',
          'mime_types' => $this->mime_types
        ));

        $this->validatorSchema['financial_docs_file']->addMessage('mime_types', 'Формат файла не поддерживается');
        $this->validatorSchema['financial_docs_file']->addMessage('max_size', 'Файл слишком большой (максимум - это %max_size% байт)');*/

        //Additional files
        /*$this->widgetSchema['additional_file'] = new sfWidgetFormInputFile(array(
            'label' => 'Файл',
        ));

        $this->widgetSchema['financial_docs_file'] = new sfWidgetFormInputFile(array(
            'label' => 'Файл',
        ));*/

        $this->validatorSchema['cost'] = new sfValidatorNumber(array('required' => false));
        $this->validatorSchema['cost']->setMessage('invalid', '"%value%" не является числом');

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

    private function addFormField($field, $path = '', $required = false)
    {
        $this->widgetSchema[$field] = new sfWidgetFormInputFile(array(
            'label' => 'Файл',
        ));

        $this->validatorSchema[$field] = new sfValidatorFile(array(
            'required'   => $required,
            'max_size' => sfConfig::get('app_max_upload_size'),
            'path'       => sfConfig::get('sf_upload_dir').'/'. $path,
            'validated_file_class' => 'ValidatedFile',
            'mime_types' => $this->mime_types
        ));

        $this->validatorSchema[$field]->addMessage('mime_types', 'Формат файла не поддерживается %mime_type%');
        $this->validatorSchema[$field]->addMessage('max_size', 'Файл слишком большой (максимум - это %max_size% байт)');
    }

}
