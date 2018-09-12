<?php

/**
 * activity_statistic_settings actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_statistic_settings
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_statistic_settingsActions extends sfActions
{

    const FILTER_NAMESPACE = 'activities_settings';
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  
  function executeIndex(sfWebRequest $request)
  {
      $this->outputFilters();
      $this->outputResult();
  }

  function executeShow(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

    public function executeShowActivitySettings(sfWebRequest $request)
    {
        $this->setting = ActivityDealerStaticticStatusTable::getInstance()
            ->createQuery()
            ->where('dealer_id = ? and activity_id = ?', array($request->getParameter('dealerId'), $request->getParameter('activityId')))
            ->fetchOne();
    }

    public function executeApplyActivitySettings(sfWebRequest $request)
    {
        $setting = ActivityDealerStaticticStatusTable::getInstance()
            ->createQuery()
            ->where('dealer_id = ? and activity_id = ?', array($request->getParameter('dealerId'), $request->getParameter('activityId')))
                ->fetchOne();

        if($setting) {
            $quarter = $request->getParameter('quarter');

            if($quarter == -1) {
                for($i = 1; $i <= 4; $i++) {
                    $qFunc = 'setQ'.$i;
                    $setting->$qFunc($request->getParameter('complete') == 1 ? $i : 0);
                }
            }
            else {
                $qFunc = 'setQ'.$request->getParameter('quarter');
                $setting->$qFunc($request->getParameter('complete') == 1 ? $request->getParameter('quarter') : 0 );
            }

            $canChangeStatus = true;
            if($request->getParameter('complete') == 0) {
                for($i = 1; $i <= 4; $i++) {
                    $qFunc = 'getQ'.$i;
                    if($setting->$qFunc() != 0) {
                        $canChangeStatus = false;
                    }
                }
            }

            if($canChangeStatus) {
                $setting->setComplete($request->getParameter('complete'));
            }

            $setting->setAlwaysOpen($request->getParameter('alwaysOpen'));
            $setting->save();
        }

        return sfView::NONE;
    }

    private function outputFilters() {
        $this->outputDealerFilter();
    }

    private function outputResult() {
        $this->dealers = DealerTable::getVwDealersQuery()->execute();

        $this->activitiesSettings = array();
        if($this->getDealerFilter() != -1) {
            $this->activitiesSettings = ActivityDealerStaticticStatusTable::getInstance()
                                    ->createQuery()
                                    ->where('dealer_id = ?', $this->getDealerFilter())
                                    ->orderBy('activity_id DESC')
                                    ->execute();
        }
    }

    private function getDealerFilter()
    {
        $default = $this->getUser()->getAttribute('dealer', -1, self::FILTER_NAMESPACE);
        $dealer = $this->getRequestParameter('sbDealer', $default);
        $this->getUser()->setAttribute('dealer', $dealer, self::FILTER_NAMESPACE);

        return $dealer;
    }

    private function outputDealerFilter() {
        $this->dealerFilter = $this->getDealerFilter();
    }
}
