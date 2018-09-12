<?php

/**
 * Helps to build statistic by dealers
 *
 * @author Сергей
 */
class AgreementDealersStatisticBuilder
{
    protected $dealers = array();
    protected $managers = array();
    protected $year;
    /**
     * User to count unread messages
     *
     * @var User
     */
    protected $user;

    function __construct($year, User $user)
    {
        $this->year = $year;
        $this->user = $user;

    }

    /**
     * Builds statistic
     *
     * @return array
     */
    function build()
    {
        $this->loadDealers();
        $this->loadBudget();
        $this->loadUnreadDiscussions();
        $this->loadBonuses();
        $this->loadActivities();

        return $this->managers;
    }

    function getManagers()
    {
        return $this->managers;
    }

    /**
     * Returns statistic by dealer
     *
     * @param Dealer $dealer
     */
    function getDealerStat(Dealer $dealer)
    {
        return isset($this->dealers[$dealer->getId()])
            ? $this->dealers[$dealer->getId()]
            : $this->createDealerBlankStat($dealer);
    }

    protected function loadDealers()
    {
        $this->show_data = true;

        $this->dealers = array();
        $this->managers = array();

        $user_dealers_list = array();

        if ($this->user->isRegionalManager()) {
            $userDealers = $this->user->hasDealersListFromNaturalPerson();

            foreach ($userDealers as $k => $i) {
                $user_dealers_list[] = $k;
            }

            if (empty($userDealers)) {
                $this->show_data = false;
            }
        }

        $query = DealerTable::getVwDealersQuery()
            ->leftJoin('d.RegionalManager rm')
            ->orderBy('rm.firstname, rm.surname');

        if (!empty($user_dealers_list)) {
            $query->andWhereIn('d.id', $user_dealers_list);
        }

        if ($this->show_data)
        {
            $all_dealers = $query->execute();
            foreach ($all_dealers as $dealer) {
                $manager = $dealer->getNfzRegionalManager();
                $manager_id = $manager ? $manager->getId() : 0;

                if (!isset($this->managers[$manager_id])) {
                    $this->managers[$manager_id] = array(
                        'manager' => $manager ? $manager->getFirstName() . ' ' . $manager->getSurname() : 'Без менеджера',
                        'dealers' => array()
                    );
                }

                $this->managers[$manager_id]['dealers'][] = $dealer;
                $this->dealers[$dealer->getId()] = $this->createDealerBlankStat($dealer);
            }
        }
    }

    protected function loadBudget()
    {
        $this->loadPlanBudget();
        $this->loadRealBudget();
    }

    protected function loadPlanBudget()
    {
        if (!$this->dealers)
            return;

        $all_budgets = BudgetTable::getInstance()
            ->createQuery()
            ->where('year=?', $this->year)
            ->andWhereIn('dealer_id', array_keys($this->dealers))
            ->execute();

        foreach ($all_budgets as $budget) {
            $this->dealers[$budget->getDealerId()]['quarters'][$budget->getQuarter()]['plan'] = $budget->getPlan();
            $this->dealers[$budget->getDealerId()]['total']['plan'] += $budget->getPlan();
        }
    }

    protected function loadRealBudget()
    {
        if (!$this->dealers)
            return;

        $all_budgets = RealTotalBudgetTable::getInstance()
            ->createQuery()
            ->where('year=?', $this->year)
            ->andWhereIn('dealer_id', array_keys($this->dealers))
            ->execute();

        foreach ($all_budgets as $budget) {
            //$this->dealers[$budget->getDealerId()]['quarters'][$budget->getQuarter()]['fact'] = $budget->getSum();
            $this->dealers[$budget->getDealerId()]['total']['fact'] += $budget->getSum();
        }


        foreach ($this->dealers as $k => $v) {
            $dealer = $v['dealer'];

            if (!empty($dealer)) {
                /*$real = new RealBudgetCalculator($dealer, $this->year);

                $this->dealers[$k]['real'] = $real->calculate();*/

                $calcReal = DealerWorkStatisticTable::getInstance()
                    ->createQuery()
                    ->select('q1, q2, q3, q4')
                    ->where('dealer_id = ? and calc_year = ?',
                        array(
                            $dealer->getId(),
                            $this->year
                        )
                    )
                    ->orderBy('id DESC')
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

                $real = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
                for($q = 1; $q <= 4; $q++) {
                    $real[$q] = (float)$calcReal['q'.$q];
                }

                $this->dealers[$k]['real'] = $real;
            }
        }
    }

    protected function loadBonuses()
    {
        $bonuses = DealerBonusTable::getInstance()
            ->createQuery()
            ->where('year=?', $this->year)
            ->execute();

        foreach ($bonuses as $bonus) {
            $this->dealers[$bonus->getDealerId()]['quarters'][$bonus->getQuarter()]['bonus'] = array(
                'bonus' => $bonus->getBonus(),
                'comment' => $bonus->getComment()
            );
        }
    }

    protected function loadActivities()
    {


        foreach ($this->dealers as $k => $v) {
            //$dealer = DealerTable::getInstance()->find($k);
            if (isset($v['dealer'])) {
                $dealer = $v['dealer'];

                if (!empty($dealer)) {
                    $full_stat = ActivityTable::getInstance()->getAcceptStat($this->year, $dealer);

                    $this->dealers[$k]['activities'] = $full_stat;
                }
            }
        }
    }

    protected function loadUnreadDiscussions()
    {
        if (!$this->dealers)
            return;

        $sql = 'create temporary table read_discussion'
            . ' select dd.dealer_id, dlr.message_id'
            . ' from dealer_discussion dd'
            . ' inner join message m on(dd.discussion_id=m.discussion_id)'
            . ' inner join discussion_last_read dlr on(dlr.message_id=m.id and dlr.user_id=' . $this->user->getId() . ')'
            . ' where dd.dealer_id in(' . implode(',', array_keys($this->dealers)) . ')'
            . ' order by m.id DESC';

        Doctrine_Manager::connection()->exec($sql);
        Doctrine_Manager::connection()->exec('create index dealer on read_discussion (dealer_id)');

        $this->loadNeverReadDiscussions();
        $this->loadOnceReadDiscussions();

        Doctrine_Manager::connection()->exec('drop table read_discussion');
    }

    protected function loadNeverReadDiscussions()
    {
        $sql = 'select dd.dealer_id, count(m.id) unread, m.id'
            . ' from dealer_discussion dd'
            . ' inner join message m on(dd.discussion_id=m.discussion_id)'
            . ' left join read_discussion rd on(dd.dealer_id=rd.dealer_id)'
            . ' where rd.dealer_id is null'
            //. ' where m.id > rd.message_id'
            . ' group by dd.dealer_id';

        $statement = Doctrine_Manager::connection()->execute($sql);
        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $this->dealers[$row->dealer_id]['unread'] = $row->unread;
        }
    }

    protected function loadOnceReadDiscussions()
    {
        $sql = 'select dd.dealer_id, count(m.id) unread'
            . ' from dealer_discussion dd'
            . ' inner join message m on(dd.discussion_id=m.discussion_id)'
            . ' inner join read_discussion rd on(dd.dealer_id=rd.dealer_id)'
            . ' where m.id > rd.message_id'
            . ' group by dd.dealer_id';

        $statement = Doctrine_Manager::connection()->execute($sql);
        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $this->dealers[$row->dealer_id]['unread'] = $row->unread;
        }
    }

    protected function createDealerBlankStat(Dealer $dealer)
    {
        $stat = array(
            'dealer' => $dealer,
            'quarters' => array(),
            'total' => array(
                'fact' => 0,
                'plan' => 0
            ),
            'unread' => 0,
            'activities' => array(),
            'real' => array()
        );
        for ($n = 1; $n <= 4; $n++) {
            $stat['quarters'][$n] = array(
                'fact' => 0,
                'plan' => 0,
                'bonus' => array('bonus' => null, 'comment' => ''),
            );
        }

        return $stat;
    }
}
