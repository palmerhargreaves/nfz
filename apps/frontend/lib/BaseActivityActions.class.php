<?php

/**
 * Description of sfActivityActions
 *
 * @author Сергей
 */
class BaseActivityActions extends ActionsWithJsonForm
{
    const FILTER_Q_NAMESPACE = 'activity_q';
    const FILTER_YEAR_NAMESPACE = 'activity_year';

    /**
     * A module identifier to check for match an action with the module.
     * False to do not check.
     *
     * @var mixed
     */
    protected $check_for_module = false;

    function outputActivity(sfWebRequest $request)
    {
        $this->activity = $this->getActivity($request);
        $this->year = $request->getParameter('year') ?: date('Y');

        //Save last activity index to session
        sfContext::getInstance()->getUser()->setAttribute('last_activity_id', $this->activity->getId());
    }

    /**
     * Returns an activity instance
     *
     * @param sfWebRequest $request
     * @return Activity
     */
    protected function getActivity(sfWebRequest $request)
    {
        $activity = ActivityTable::getInstance()->find($request->getParameter('activity'));
        $this->forward404Unless($activity);

        if ($this->check_for_module) {
            $match = false;
            foreach ($activity->getModules() as $module) {
                if ($module->getIdentifier() == $this->check_for_module) {
                    $match = true;
                    break;
                }
            }
            if (!$match)
                throw new ActionDoesNotMatchModuleException();
        }

        return $activity;
    }

    public function outputFilterByQuarter()
    {
        $default = $this->getUser()->getAttribute('current_q', D::getQuarter(D::calcQuarterData(time())), self::FILTER_Q_NAMESPACE);
        if ($default == 0) {
            $default = D::getQuarter(D::calcQuarterData(time()));
        }

        $q = $this->getRequestParameter('current_q');
        if (is_null($q)) {
            $q = $this->getRequestParameter('quarter', $default);
        }
        $this->getUser()->setAttribute('current_q', $q, self::FILTER_Q_NAMESPACE);

        $this->current_q = $q;
    }

    public function outputFilterByYear()
    {
        $req_year = $this->getRequestParameter('year');
        if (!empty($req_year)) {
            $this->getUser()->setAttribute('current_year', $req_year, self::FILTER_YEAR_NAMESPACE);
            $year = $req_year;
        }
        else {
            $default = $this->getUser()->getAttribute('current_year', D::getYear(D::calcQuarterData(time())), self::FILTER_YEAR_NAMESPACE);
            $year = $this->getRequestParameter('current_year', $default);
            $this->getUser()->setAttribute('current_year', $year, self::FILTER_YEAR_NAMESPACE);
        }

        $this->current_year = $year;
    }

    public function outputModelByQ(sfWebRequest $request)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));

        $this->open_model = $request->getParameter('model');

        if ($this->getUser()->getAttribute('current_q') != $this->current_q) {
            $this->getUser()->setAttribute('current_q', $this->current_q);

            if (is_null($this->open_model)) {
                $this->redirect(url_for('@agreement_module_models_q?activity=' . $this->getActivity($request)->getId() . '&quarter=' . $this->current_q));
            } else {
                $this->redirect(url_for('@agreement_module_models_model_with_q?activity=' . $this->getActivity($request)->getId().'&model='.$this->open_model.'&quarter=' . $this->current_q));
            }
        }
    }

    /**
     * Get models list by quarters
     * @param sfWebRequest $request
     * @throws ActionDoesNotMatchModuleException
     */
    public function outputModelsQuarters(sfWebRequest $request)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));

        $quartersModels = new ActivityQuartersModelsAndStatistics($this->getUser()->getAuthUser(), $this->getActivity($request));
        $qData = $quartersModels->getData();

        $qList = array();
        $yearsList = array();

        foreach ($qData as $y_key => $q_data) {
            $qList = array_merge($qList, array_map(function($key) {
                return $key;
            }, array_keys($q_data))) ;

            if (!in_array($y_key, $yearsList)) {
                $yearsList[] = $y_key;
            }
        }

        $current_date = D::calcQuarterData(time());
        $currentQ = D::getQuarter($current_date);
        $currentY = D::getYear($current_date);

        $q = $this->getUser()->getAttribute('current_q', D::getQuarter(time()), self::FILTER_Q_NAMESPACE);
        $year = $this->getUser()->getAttribute('current_year', D::getYear(time()), self::FILTER_Q_NAMESPACE);

        if ($q == 0 || (!in_array($q, $qList) && !empty($qList))) {
            $q = isset($qList[0]) ? max($qList) : D::getQuarter(D::calcQuarterData(time()));
        }

        if (!empty($year) && !in_array($year, $yearsList)) {
            $year = isset($yearsList[0]) ? $yearsList[0] : D::getYear(D::calcQuarterData(time()));
        }

        if (!empty($q) && $q != 0) {
            $this->year = $year;
            $this->current_q = $q;
            $this->default_module = 'agreement';

            $this->getUser()->setAttribute('current_q', $this->current_q, self::FILTER_Q_NAMESPACE);
            $this->getUser()->setAttribute('current_year', $this->year, self::FILTER_YEAR_NAMESPACE);
        } else {
            if (in_array($currentQ, $qList)) {
                $this->current_q = $currentQ;
            } else {
                $this->current_q = count($qList) > 0 ? $qList[0] : null;
            }

            if (in_array($currentY, $yearsList)) {
                $this->current_year = $currentY;
            } else {
                $this->current_q = count($yearsList) > 0 ? $yearsList[0] : null;
            }

            if ($this->getUser()->getAttribute('current_q') != $this->current_q) {
                $this->getUser()->setAttribute('current_q', $this->current_q, self::FILTER_Q_NAMESPACE);
                $this->getUser()->setAttribute('current_year', $this->current_year, self::FILTER_YEAR_NAMESPACE);

                $this->redirect(url_for('@activity_quarter_data?activity='.$this->getActivity($request)->getId().'&current_q='.$this->current_q.'&current_year='.$this->current_year));
            }
        }

        $this->quartersModels = $quartersModels;
        $this->open_model = $request->getParameter('model');
    }
}
