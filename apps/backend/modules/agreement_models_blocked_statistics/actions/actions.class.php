<?php

/**
 * activity_statistic_settings actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_statistic_settings
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agreement_models_blocked_statisticsActions extends sfActions
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */

    function executeIndex(sfWebRequest $request)
    {
        $util = new AgreementModelsBlockedStatisticUtils();

        $this->models = $util->getData();
    }

    function executeShow(sfWebRequest $request)
    {
        $this->setTemplate('index');
    }

    function executeShowModelBlockedInfo(sfWebRequest $request)
    {
        $model_id = $request->getParameter('model_id');

        $blocked_model = AgreementModelsBlokedStatisticsTable::getInstance()->findOneByModelId($model_id);
        if ($blocked_model) {
            $this->history_items = AgreementModelsBlokedStatisticsItemsTable::getInstance()->createQuery()->where('parent_id = ?', $blocked_model['id'])->orderBy('id DESC')->execute();
        } else {
            $this->history_items = array();
        }
    }
}
