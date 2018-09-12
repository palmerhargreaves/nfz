<?php

/**
 * MaterialSource form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MaterialSourceForm extends BaseMaterialSourceForm
{
    public function configure()
    {
        unset($this['created_at'], $this['updated_at']);

        $this->widgetSchema['material_id'] = new sfWidgetFormInputHidden();

        $this->widgetSchema['file'] = new WidgetFormFile(array(
            'label' => 'Файл для загрузки',
            'file_src' => '/uploads/' . MaterialSource::FILE_PATH,
            'with_delete' => false
        ));

        $server_files = array_merge(array(''), F::getFiles(sfConfig::get('sf_upload_dir') . '/' . MaterialSource::SERVER_FILES_PATH));
        $server_files = array_combine($server_files, $server_files);
        $this->widgetSchema['server_file'] = new sfWidgetFormChoice(array(
            'label' => 'Файл на сервере',
            'choices' => $server_files
        ));

        $this->validatorSchema['file'] = new sfValidatorFile(array(
            'required' => false,
            'path' => sfConfig::get('sf_upload_dir') . '/' . MaterialSource::FILE_PATH,
            'validated_file_class' => 'ValidatedFile',
            /*'mime_types' => array(
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
              'video/x-ms-wmv'
            ),*/
        ));

        $this->validatorSchema['server_file'] = new sfValidatorChoice(array(
            'required' => false,
            'choices' => array_keys($server_files)
        ));

        $this->mergePostValidator(new sfValidatorCallback(array(
            'callback' => array($this, 'validateFile')
        )));

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

    function validateFile(sfValidatorCallback $validator, $values)
    {
        if (
            !$this->getObject()->isNew()
            || isset($values['file']) && $values['file']
            || isset($values['server_file']) && $values['server_file']
        )
            return $values;

        $validator->setMessage('required', 'Необходимо выбрать файл');
        throw new sfValidatorError($validator, 'required');
    }
}
