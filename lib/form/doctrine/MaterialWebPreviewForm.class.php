<?php

/**
 * MaterialWebPreview form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MaterialWebPreviewForm extends BaseMaterialWebPreviewForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at']);
    
    $this->widgetSchema['material_id'] = new sfWidgetFormInputHidden();
    
    $this->widgetSchema['file'] = new sfWidgetFormInputFile(array(
      'label' => 'Файл',
    ));
    
    $this->validatorSchema['file'] = new sfValidatorFile(array(
      'required'   => true,
      'validated_file_class' => 'ValidatedFile',
      'path'       => sfConfig::get('sf_upload_dir').'/'.MaterialWebPreview::FILE_PATH,
      'mime_types' => 'web_images',
    ));
  }
}
