<?php

require_once dirname(__FILE__) . '/../lib/activity_efficiency_formulas_paramsGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/activity_efficiency_formulas_paramsGeneratorHelper.class.php';

/**
 * activity_efficiency_formulas_params actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_efficiency_formulas_params
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_efficiency_formulas_paramsActions extends autoActivity_efficiency_formulas_paramsActions
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

        $this->form->setDefault('formula_id', $request->getParameter('formula_id'));
    }

    function executeNew(sfWebRequest $request)
    {
        parent::executeNew($request);

        $this->form->bind(array(
            'formula_id' => $request->getParameter('formula_id')
        ), array());
    }

    function redirect($url, $statusCode = 302)
    {
        if ($url == '@activity_efficiency_formulas_params_new' && $this->form) {
            $url .= '?formula_id='.$this->form->getValue('formula_id');
        } else if ($url == '@activity_efficiency_formulas_params_edit' && $this->form) {
            $url .= '?formula_id=' . $this->form->getValue('formula_id');
        }

        parent::redirect($url, $statusCode);
    }

    public function executeLoadParamData(sfWebRequest $request)
    {
        $formula_utils = new ActivityFormulasUtils($request->getParameter('formula_id'), $request->getParameter('param'));

        $this->values = $formula_utils->build();
    }
}
