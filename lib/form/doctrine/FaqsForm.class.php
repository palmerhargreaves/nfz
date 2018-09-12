<?php

/**
 * Faqs form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FaqsForm extends BaseFaqsForm
{
  public function configure()
  {
  	unset($this['created_at'], $this['updated_at']);

  	$this->widgetSchema['image'] = new WidgetFormFile(array(
      'label' => 'Картинка',
      'delete_label' => 'Удалить файл',
      'file_src' => '/uploads/'.News::NEWS_IMAGES
    ));

    $this->widgetSchema->setPositions(array(
      'id',
      'question',
      'answer',
      'image',
      'status'
    ));

  	$this->validatorSchema['image'] = new sfValidatorFile(array(
      'required'   => false,
      'path'       => sfConfig::get('sf_upload_dir').'/'.News::NEWS_IMAGES,
      'validated_file_class' => 'ValidatedFile',
      'mime_types' => array(
        'image/jpeg',
        'image/pjpeg',
        'image/gif',
        'image/png',
        'image/x-png',
      ),
    ));

    $this->validatorSchema['image_delete'] = new sfValidatorBoolean();
    
   }
}
