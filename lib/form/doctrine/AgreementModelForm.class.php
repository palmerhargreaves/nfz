<?php

/**
 * AgreementModel form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AgreementModelForm extends BaseAgreementModelForm
{
    const MAX_FILES = 10;

    protected $draft;

    private $editor;
    private $mime_types;

    private $_extracted_fields = array();
    private $_extracted_files = array();

    private $_values = null;

    public function __construct($draft = false, $object = null, $options = array(), $CSRFSecret = null, $editor = false)
    {
        $this->draft = $draft;
        $this->editor = $editor;

        parent::__construct($object, $options, $CSRFSecret);
    }

    public function configure()
    {
        unset($this['created_at'], $this['updated_at'], $this['agreement_comments'], $this['agreement_comments_file'], $this['report_id'], $this['discussion_id'], $this['model_accepted_in_online_redactor']);

        if ($this->getObject()->getId()) {
            unset($this['editor_link']);
        }

        $this->widgetSchema['blank_id'] = new sfWidgetFormInputHidden();

        $this->widgetSchema['is_valid_data'] = new sfWidgetFormInputHidden();

        $this->widgetSchema['model_type_id'] = new sfWidgetFormDoctrineChoice(array(
            'model' => $this->getRelatedModelName('ModelType'),
            'add_empty' => false,
            'query' => AgreementModelTypeTable::getInstance()
                ->createQuery()->where('concept=?', 0)
        ));

        $this->widgetSchema['task_id'] = new sfWidgetFormInputHidden();
        /*$this->widgetSchema['model_file'] = new sfWidgetFormInputFile(array(
            'label' => 'Файл',
        ));*/

        /*$this->validatorSchema['model_file'] = new sfValidatorFile(array(
            'required' => false,
            'max_size' => sfConfig::get('app_max_upload_size'),
            'path' => sfConfig::get('sf_upload_dir') . '/' . AgreementModel::MODEL_FILE_PATH,
            'validated_file_class' => 'ValidatedFile',
            'mime_types' => $this->getMimeTypes()
        ));
        $this->validatorSchema['model_file']->addMessage('mime_types', 'Формат файла не поддерживается (%mime_type%)');
        $this->validatorSchema['model_file']->addMessage('max_size', 'Файл слишком большой (максимум - это %max_size% байт)');*/

        /*if (!Utils::allowedIps()) {
            $this->_extracted_fields[] = 'model_file';
            $this->_extracted_fields[] = 'model_record_file';

            for ($i = 1; $i <= sfConfig::get('app_max_files_upload_count'); $i++) {
                $this->widgetSchema['model_file_' . $i] = new sfWidgetFormInputText();
                $this->widgetSchema['model_record_file_' . $i] = new sfWidgetFormInputText();

                $this->validatorSchema['model_file_' . $i] = new sfValidatorString(array('max_length' => 255, 'required' => false));
                $this->validatorSchema['model_record_file_' . $i] = new sfValidatorString(array('max_length' => 255, 'required' => false));

                $this->_extracted_fields[] = 'model_file_' . $i;
                $this->_extracted_fields[] = 'model_record_file_' . $i;
            }

            $this->addFormField('model_record_file');

            $this->setupScenarioRecordFields();
        }*/

        $this->validatorSchema['cost']->setMessage('invalid', '"%value%" не является числом');
        $this->validatorSchema['accept_in_model']->setMessage('invalid', '"%value%" не является числом');

        $this->validatorSchema['blank_id'] = new sfValidatorString(array(
            'required' => false
        ));

        $this->validatorSchema['task_id'] = new sfValidatorString(array(
            'required' => true
        ));

        $this->getValidatorSchema()->setPostValidator(new sfValidatorCallback(array(
            'callback' => array($this, 'validateForUniqueConcept')
        ), array('invalid' => 'Концепция уже загружена')));

        foreach ($this->validatorSchema->getFields() as $name => $validator) {
            if ($this->draft && $name != 'name')
                $validator->setOption('required', false);

            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

    public function validateForUniqueConcept($validator, $values)
    {
        if (!$this->getObject()->isNew())
            return $values;

        $dealer = DealerTable::getInstance()->find($values['dealer_id']);
        $activity = ActivityTable::getInstance()->find($values['activity_id']);
        $type = AgreementModelTypeTable::getInstance()->find($values['model_type_id']);
        /*if($activity && $dealer && $type && $type->getConcept() && AgreementModelTable::getInstance()->hasConcept($activity, $dealer))
          throw new sfValidatorError($validator, 'invalid');*/

        return $values;
    }

    private function addFormField($field, $mimeTypes = null, $max_file_size = null, $req = false, $check_for_ms_files = true)
    {
        $this->widgetSchema[$field] = new sfWidgetFormInputFile(array(
            'label' => 'Файл',
        ));

        if (is_null($max_file_size)) {
            $max_file_size = sfConfig::get('app_max_upload_size');
        }

        $this->validatorSchema[$field] = new ValidatorFile(array(
            'required' => $req,
            'max_size' => $max_file_size,
            'path' => sfConfig::get('sf_upload_dir') . '/' . AgreementModel::MODEL_FILE_PATH,
            'validated_file_class' => 'ValidatedFile',
            //'mime_types' => $this->mime_types
            'mime_types' => is_null($mimeTypes) ? $this->getMimeTypes() : $mimeTypes
        ));

        $this->validatorSchema[$field]->setCheckForMsFiles($check_for_ms_files);

        $this->validatorSchema[$field]->addMessage('mime_types', 'Формат файла не поддерживается %mime_type%');
        $this->validatorSchema[$field]->addMessage('max_size', 'Файл слишком большой (максимум - это %max_size% байт)');
    }

    private function getMimeTypes()
    {
        return array(
            'image/jpeg',
            'image/png',
            'image/x-png',
            'image/gif',
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
    }

    private function getMimeTypeForScenario()
    {
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
    }

    private function getMimeTypeForRecords()
    {
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
    }
}
