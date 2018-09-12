<?php

require_once dirname(__FILE__) . '/../lib/activity_efficiency_work_formulasGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/activity_efficiency_work_formulasGeneratorHelper.class.php';

/**
 * activity_efficiency_work_formulas actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_fields
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_efficiency_work_formulasActions extends autoActivity_efficiency_work_formulasActions
{
    function executeNew(sfWebRequest $request)
    {
        parent::executeNew($request);
    }

    function redirect($url, $statusCode = 302)
    {
        if ($url == '@activity_efficiency_work_formulas_new' && $this->form) {
        }

        parent::redirect($url, $statusCode);
    }

    protected function buildQuery()
    {
        $query = parent::buildQuery();

        return $query->orderBy('position ASC');
    }

    public function executeReorder(sfWebRequest $request)
    {
        $data = json_decode($request->getParameter('data'));

        $ind = 1;
        foreach ($data->{'effectiveness-formulas-list'} as $key) {
            if (!empty($key) && is_numeric($key)) {

                $activity = ActivityEfficiencyWorkFormulasTable::getInstance()->find($key);
                if ($activity) {
                    $activity->setPosition($ind);
                    $activity->save();
                }

                $ind++;
            }
        }

        return sfView::NONE;
    }
}
