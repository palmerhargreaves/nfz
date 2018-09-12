<?php

require_once dirname(__FILE__) . '/../lib/activity_video_records_statistics_headers_fieldsGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/activity_video_records_statistics_headers_fieldsGeneratorHelper.class.php';

/**
 * activity_fields actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_fields
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_video_records_statistics_headers_fieldsActions extends autoActivity_video_records_statistics_headers_fieldsActions
{
    private $_parent_id = 0;
    private $_request = null;

    public function preExecute()
    {
        //$this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
        //$this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));

        parent::preExecute();
    }

    function executeIndex(sfWebRequest $request) {
        $this->redirect('@activity_video_records_statistics?activity_id='.$this->getUser()->getAttribute('activity_id', -1, ActivityVideoRecordsStatistics::FILTER_NAMESPACE));
    }

    function executeEdit(sfWebRequest $request) {
        parent::executeEdit($request);

        $activity_id = 0;

        $field_data = ActivityFieldsTable::getInstance()->find($request->getParameter('id'));
        if ($field_data && $field_data->getParentHeaderId()) {
            $field_header = ActivityVideoRecordsStatisticsHeadersTable::getInstance()->find($field_data->getParentHeaderId());
            if ($field_header) {
                $parent = ActivityVideoRecordsStatisticsTable::getInstance()->find($field_header->getParentId());
                $activity_id = $parent->getActivityId();
            }
        }

        if ($activity_id != 0) {
            $this->form->setDefault('activity_id', $activity_id);
        }
    }

    function executeNew(sfWebRequest $request)
    {
        parent::executeNew($request);

        $this->getUser()->setAttribute('activity_id', $request->getParameter('activity_id'), ActivityVideoRecordsStatistics::FILTER_NAMESPACE);

        $this->_parent_id = $request->getParameter('parent_id');
        $this->form->bind(array(
            'parent_header_id' => $request->getParameter('parent_id'),
            'activity_id' => $request->getParameter('activity_id')
        ), array());
    }

    function redirect($url, $statusCode = 302)
    {
        if ($url == '@activity_video_records_statistics_headers_fields_new' && $this->form) {
            $url .= '?parent_id=' . $this->form->getValue('parent_header_id').'&activity_id='.$this->getUser()->getAttribute('activity_id', -1, ActivityVideoRecordsStatistics::FILTER_NAMESPACE);
        }

        parent::redirect($url, $statusCode);
    }
}
