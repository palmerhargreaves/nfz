<?php

/**
 * Form to decline an activity model
 *
 * @author Сергей
 */
class AgreementDeclineForm extends BaseForm
{
    const MAX_FILES = 10;

    function configure()
    {
        $widgets = array(
            'agreement_comments' => new sfWidgetFormTextarea(array(
                'label' => 'Комментарии'
            )),
            'agreement_comments_file' => new sfWidgetFormInputFile(array(
                'label' => 'Файл с комментариями',
            )),
            'designer_approve' => new sfWidgetFormInputCheckbox(array(
                'label' => 'С утверждением дизайнера'
            ))
        );

        for ($ind = 1; $ind <= sfConfig::get('app_max_files_upload_count'); $ind++) {
            $widgets['agreement_comments_file_' . $ind] = new sfWidgetFormInputFile(array(
                'label' => 'Файл с комментариями'
            ));
        }

        $reason_model = $this->getOption('reason_model');
        if ($reason_model) {
            $widgets['decline_reason_id'] = new sfWidgetFormDoctrineChoice(array(
                'model' => $reason_model,
                'label' => 'Причина'
            ));
        }

        $this->setWidgets($widgets);

        $validators = array(
            'agreement_comments' => new sfValidatorString(array(
                'required' => false
            )),
            'agreement_comments_file' => new sfValidatorFile(
                array(
                    'required' => false,
                    'path' => sfConfig::get('sf_upload_dir') . '/' . $this->getOption('comments_file_path'),
                    'validated_file_class' => 'ValidatedFile',
                    'mime_types' => $this->getMimeTypes()
                ),
                array(
                    'mime_types' => 'Формат файла не поддерживается'
                )
            ),
            'designer_approve' => new sfValidatorBoolean(
                array(
                    'required' => false
                )
            )
        );

        for ($ind = 1; $ind <= sfConfig::get('app_max_files_upload_count'); $ind++) {
            $validators['agreement_comments_file_' . $ind] = new sfValidatorFile(
                array
                (
                    'required' => false,
                    'path' => sfConfig::get('sf_upload_dir') . '/' . $this->getOption('comments_file_path'),
                    'validated_file_class' => 'ValidatedFile',
                    'mime_types' => $this->getMimeTypes()
                )
            );
        }

        if ($reason_model) {
            $validators['decline_reason_id'] = new sfValidatorDoctrineChoice(array(
                'model' => $this->getOption('reason_model')
            ));
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
            'video/x-ms-wmv');
    }
}
