<?php

/**
 * Form to comment agreement model
 *
 * @author kostig51
 */
class AgreementCommentsForm extends BaseForm
{
    function configure()
    {
        $widgets = array(
            'comments_files' => new sfWidgetFormInputFile(array(
                'label' => 'Файл'
            )),
        );

        for ($ind = 1; $ind <= sfConfig::get('app_max_files_upload_count'); $ind++) {
            $widgets['comments_files_' . $ind] = new sfWidgetFormInputFile(array(
                'label' => 'Файл с комментариями'
            ));
        }

        $this->setWidgets($widgets);

        $validators = array(
            'comments_files' => new sfValidatorFile(
                array(
                    'required' => false,
                    'path' => sfConfig::get('sf_upload_dir') . '/' . $this->getOption('comments_file_path'),
                    'validated_file_class' => 'ValidatedFile',
                    'mime_types' => $this->getMimeTypes()
                ),
                array(
                    'mime_types' => 'Формат файла не поддерживается'
                )
            )
        );

        for ($ind = 1; $ind <= sfConfig::get('app_max_files_upload_count'); $ind++) {
            $validators['comments_files_' . $ind] = new sfValidatorFile(
                array(
                    'required' => false,
                    'path' => sfConfig::get('sf_upload_dir') . '/' . $this->getOption('comments_file_path'),
                    'validated_file_class' => 'ValidatedFile',
                    'mime_types' => $this->getMimeTypes()
                ),
                array(
                    'mime_types' => 'Формат файла не поддерживается'
                )
            );
        }

        $this->setValidators($validators);

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
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
            'video/x-ms-wmv'
        );
    }
}
