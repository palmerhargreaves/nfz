<?php

require_once dirname(__FILE__) . '/../lib/activity_efficiency_formulasGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/activity_efficiency_formulasGeneratorHelper.class.php';

/**
 * activity_fields actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_fields
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_efficiency_formulasActions extends autoActivity_efficiency_formulasActions
{
    private $_parent_id = 0;
    private $_request = null;

    public function preExecute()
    {
        $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
        //$this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));

        parent::preExecute();
    }

    function executeIndex(sfWebRequest $request) {
        $this->redirect('@activity_video_records_statistics?activity_id='.$this->getUser()->getAttribute('activity_id', -1, ActivityVideoRecordsStatistics::FILTER_NAMESPACE));
    }

    function executeNew(sfWebRequest $request)
    {
        parent::executeNew($request);

        $this->form->bind(array(
            'activity_id' => $request->getParameter('activity_id')
        ), array());
    }

    function onSaveObject(sfEvent $event)
    {
        $object = $event['object'];

        $work_formula = ActivityEfficiencyWorkFormulasTable::getInstance()->find($object->getWorkFormulaId());
        if ($work_formula) {
            $object->setName($work_formula->getName());
            $object->save();
        }
    }

    function redirect($url, $statusCode = 302)
    {
        if ($url == '@activity_efficiency_formulas_new' && $this->form) {
            $url .= '?activity_id='.$this->form->getValue('activity_id');
        }

        parent::redirect($url, $statusCode);
    }
}
