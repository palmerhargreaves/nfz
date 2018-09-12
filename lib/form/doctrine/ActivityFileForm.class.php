<?php

/**
 * ActivityFile form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ActivityFileForm extends BaseActivityFileForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at']);
    
    $this->widgetSchema['activity_id'] = new sfWidgetFormInputHidden();
    
    $this->widgetSchema['file'] = new sfWidgetFormInputFile(array(
      'label' => 'Файл',
    ));
    
    $this->validatorSchema['file'] = new sfValidatorFile(array(
      'required'   => true,
      'path'       => sfConfig::get('sf_upload_dir').'/'. ActivityFile::FILE_PATH,
      'validated_file_class' => 'ValidatedFile',
      'mime_types' => array(
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
      ),
    ));
    
    foreach ($this->validatorSchema->getFields() as $validator)
    {
      $validator->setMessage('required', 'Обязательно для заполнения');
    }
  }
}
