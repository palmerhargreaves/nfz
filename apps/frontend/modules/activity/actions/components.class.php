<?php

class activityComponents extends sfComponents
{
    function executeNotFinishedActivities(sfWebRequest $request)
    {
        $user = $this->getUser();
        $show_hidden = $user->isAdmin() || $user->isImporter() || $user->isManager();

        $query = ActivityTable::getInstance()
            ->createQuery('a')
            ->select('a.id, a.start_date, a.end_date, a.custom_date, a.name, a.brief, a.importance, v.id is_viewed')
            ->leftJoin('a.UserViews v WITH v.user_id=?', $this->getUser()->getAuthUser()->getId())
            ->orderBy('a.position ASC, a.importance DESC, sort DESC');


        $this->year = $year = D::getBudgetYear($request);
        if ($request->getParameter('year') && !D::isSpecialFirstQuarter($request)) {
            //$query->andWhere('allow_extended_statistic = ?', true);
            $query->andWhere('year(a.start_date) = ? or year(a.end_date) = ?', array($this->year, $this->year))
                    ->andWhere('a.finished = ? ', array(false))
                    ->orWhere('allow_extended_statistic = ?', true);
        } else {
            $query->where('a.finished = ?', false);
        }

        //if(!$show_hidden)
        $query->andWhere('a.hide=?', false);
        if ($user->getAuthUser()->isAdmin()) {
            $query->orWhere('a.hide = ?', true);
        }

        ActivityTable::checkActivity($user, $query);

        $this->activities = $query->execute();
        $this->finishedActivities = false;

        $query = AgreementModelTable::getInstance()
            ->createQuery('am')
            ->select()
            ->leftJoin('am.Report r')
            ->orWhereIn('am.status', array('wait', 'wait_specialist', 'declined'))
            ->orWhereIn('r.status', array('wait', 'wait_specialist', 'declined'))
            ->orderBy('am.updated_at DESC')
            ->limit(5);

        $dealerId = DealerUserTable::getInstance()->createQuery()->select('dealer_id')->where('user_id = ?', $this->getUser()->getAuthUser()->getId())->fetchOne();
        if ($dealerId) {
            $dealer = DealerTable::getInstance()->find($dealerId->getDealerId());

            if($dealer) {
                $query->andWhere('am.dealer_id = ?', $dealer->getId());
            }
        }

        $this->models = $query->execute();
    }

    function executeFinished(sfWebRequest $request)
    {
        $year = D::getBudgetYear($request);

        $user = $this->getUser();
        $show_hidden = $user->isAdmin() || $user->isImporter() || $user->isManager();

        $query = ActivityTable::getInstance()
            ->createQuery('a')
            ->select('a.id, a.start_date, a.end_date, a.custom_date, a.name, a.brief, a.importance, v.id is_viewed')
            ->leftJoin('a.UserViews v WITH v.user_id=?', $this->getUser()->getAuthUser()->getId())
            ->where('finished=?', true)
            ->orderBy('a.importance DESC, sort DESC, a.position ASC');

        if (!$show_hidden)
            $query->andWhere('a.hide=?', false);

        ActivityTable::checkActivity($user, $query);

        $this->activities = $query->execute();

        $this->finishedActivities = true;
        $this->year = $year;
    }

    function executeDealerStatistics(sfWebRequest $request)
    {
        $dealerId = DealerUserTable::getInstance()->createQuery()->select('dealer_id')->where('user_id = ?', $this->getUser()->getAuthUser()->getId())->fetchOne();
        if ($dealerId) {
            $dealer = DealerTable::getInstance()->find($dealerId->getDealerId());

            if ($dealer) {

                $year = D::getBudgetYear($request);
                $builder = new AgreementDealerStatisticBuilder($year, $dealer);
                $builder->build();

                $this->builder = $builder;

                $this->outputDeclineReasons();
                $this->outputDeclineReportReasons();
                $this->outputSpecialistGroups();

                $this->year = $year;
            }
        }
    }

    function outputDeclineReasons()
    {
        $this->decline_reasons = AgreementDeclineReasonTable::getInstance()->createQuery()->execute();
    }

    function outputDeclineReportReasons()
    {
        $this->decline_report_reasons = AgreementDeclineReportReasonTable::getInstance()->createQuery()->execute();
    }

    function outputSpecialistGroups()
    {
        $this->specialist_groups = UserGroupTable::getInstance()
            ->createQuery('g')
            ->distinct()
            ->select('g.*')
            ->innerJoin('g.Roles r WITH r.role=?', 'specialist')
            ->innerJoin('g.Users u WITH u.active=?', true)
            ->execute();
    }

}
