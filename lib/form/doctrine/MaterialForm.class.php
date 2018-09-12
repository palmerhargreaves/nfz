<?php

/**
 * Material form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MaterialForm extends BaseMaterialForm
{
    public function configure()
    {
        unset($this['created_at'], $this['updated_at'], $this['activity_id']);

        $this->widgetSchema['activities_list'] = new sfWidgetFormDoctrineChoice(array('model' => 'Activity', 'query' => Doctrine::getTable('Activity')->getActivitesList(), 'add_empty' => false, 'multiple' => true, 'method' => 'getIdName'));
        $this->validatorsSchema['activities_list'] = new sfValidatorDoctrineChoice(array('model' => 'Activity', 'multiple' => true, 'required' => false));

        $this->widgetSchema['file_preview'] = new WidgetFormFile(array(
            'label' => 'Файл-превью',
            'delete_label' => 'Удалить файл',
            'file_src' => '/uploads/' . Material::FILE_PREVIEW_PATH
        ));

        $source = array_merge(array(''), F::getFiles(sfConfig::get('sf_upload_dir') . '/' . MaterialSource::SERVER_FILES_PATH));
        $source = array_combine($source, $source);
        $this->widgetSchema['source'] = new sfWidgetFormChoice(array(
            'label' => 'Исходный файл',
            'choices' => $source,
            'multiple' => true
        ), array(
            'size' => 10,
            'style' => 'width: 50%;'
        ));

        $web_preview = array_merge(array(''), F::getFiles(sfConfig::get('sf_upload_dir') . '/uploads_raw/materials/web_preview/'));
        $web_preview = array_combine($web_preview, $web_preview);
        $this->widgetSchema['web_preview'] = new sfWidgetFormChoice(array(
            'label' => 'Веб-превью',
            'choices' => $web_preview,
            'multiple' => true
        ), array(
            'size' => 10,
            'style' => 'width: 50%;'
        ));


        $this->widgetSchema->setPositions(array(
            'id',
            'name',
            'category_id',
            //'activity_id',
            'activities_list',
            'editor_link',
            'file_preview',
            'source',
            'web_preview',
            'material_status'
        ));

        $this->validatorSchema['file_preview'] = new sfValidatorFile(array(
            'required' => false,
            'path' => sfConfig::get('sf_upload_dir') . '/' . Material::FILE_PREVIEW_PATH,
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

        $this->validatorSchema['file_preview_delete'] = new sfValidatorBoolean();

        $this->validatorSchema['source'] = new sfValidatorChoice(array(
            'required' => false,
            'choices' => array_keys($source),
            'multiple' => true
        ));

        $this->validatorSchema['web_preview'] = new sfValidatorChoice(array(
            'required' => false,
            'choices' => array_keys($web_preview),
            'multiple' => true
        ));
    }
}
