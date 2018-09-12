<?php

require_once dirname(__FILE__) . '/../lib/activity_video_records_statistics_headersGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/activity_video_records_statistics_headersGeneratorHelper.class.php';

/**
 * activity_fields actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_fields
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_video_records_statistics_headersActions extends autoActivity_video_records_statistics_headersActions
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

    /*public function onSaveObject(sfEvent $event) {
        if ($this->_request) {
            $this->_parent_id = $this->_request->getPostParameters();
        }

        $this->executeIndex($this->_request);
    }*/

    function executeCreate(sfWebRequest $request) {
        $this->_parent_id = $request->getParameter('parent_id');
        $this->_request = $request;

        parent::executeCreate($request);
    }

    function executeNew(sfWebRequest $request)
    {
        parent::executeNew($request);

        $this->_parent_id = $request->getParameter('parent_id');
        $this->form->bind(array(
            'parent_id' => $request->getParameter('parent_id')
        ), array());
    }

    function redirect($url, $statusCode = 302)
    {
        if ($url == '@activity_video_records_statistics_headers_new' && $this->form) {
            $url .= '?parent_id=' . $this->form->getValue('parent_id').'&activity_id='.$this->getUser()->getAttribute('activity_id', -1, ActivityVideoRecordsStatistics::FILTER_NAMESPACE);
        }

        parent::redirect($url, $statusCode);
    }
}
