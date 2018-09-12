<?php

/**
 * News form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class NewsForm extends BaseNewsForm
{
  public function configure()
  {
  	unset($this['created_at'], $this['updated_at'], $this['date_of_add'], $this['is_important']);

  	$this->widgetSchema['img_small'] = new WidgetFormFile(array(
      'label' => 'Маленькая картинка',
      'delete_label' => 'Удалить файл',
      'file_src' => '/uploads/'.News::NEWS_IMAGES
    ));

    $this->widgetSchema['img_big'] = new WidgetFormFile(array(
      'label' => 'Большая картинка',
      'delete_label' => 'Удалить файл',
      'file_src' => '/uploads/'.News::NEWS_IMAGES
    ));


    
    $this->widgetSchema->setPositions(array(
      'id',
      'name',
      'announcement',
      'img_small',
      'img_big',
      'text',
      'status'
    ));

  	$this->validatorSchema['img_small'] = new sfValidatorFile(array(
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

    $this->validatorSchema['img_big'] = new sfValidatorFile(array(
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
    
    $this->validatorSchema['img_small_delete'] = new sfValidatorBoolean();
    $this->validatorSchema['img_big_delete'] = new sfValidatorBoolean();
    
   }
}
