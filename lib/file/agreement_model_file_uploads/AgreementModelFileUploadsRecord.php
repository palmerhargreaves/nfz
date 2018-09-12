<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 19.06.2017
 * Time: 17:49
 */
class AgreementModelFileUploadsRecord extends AgreementModelFileUploads
{
    protected $_file_field = 'model_record_file';

    protected $_file_model = AgreementModel::UPLOADED_FILE_RECORD;
    protected $_file_model_type = AgreementModel::UPLOADED_FILE_RECORD_TYPE;
}
