<?php

/**
 * Description of components
 *
 * @author Сергей
 */
class budgetComponents extends sfComponents
{
    function executeBudgetPanel(sfWebRequest $request)
    {
        $this->year = D::getBudgetYear($request);
        $this->isPrevYear = '';

        $this->outputPlan();
        $this->outputReal();
        $this->outputMonths();
        $this->outputCurrentQuarter();
        $this->outputQuarterEnds();
        $this->outputQuarterDays();
        $this->outputAcceptStat();
    }

    protected function outputPlan()
    {
        $dealer = $this->dealer;
        $year_plan = 0;

        if ($dealer) {
            $defined_plan = BudgetTable::getInstance()
                ->createQuery()
                ->where(
                    'dealer_id=? and year=?',
                    array($dealer->getId(), $this->year)
                )
                ->orderBy('quarter asc')
                ->execute();

            $plan = array();
            for ($n = 1; $n <= 4; $n++) {
                $empty_plan = new Budget();
                $empty_plan->setArray(array(
                    'dealer_id' => $dealer->getId(),
                    'year' => $this->year,
                    'quarter' => $n,
                    'plan' => 0
                ));
                $plan[$n] = $empty_plan;
            }

            foreach ($defined_plan as $p) {
                $plan[$p->getQuarter()] = $p;
                $year_plan += $p->getPlan();
            }
        }

        $this->plan = $plan;
        $this->year_plan = $year_plan;
    }

    protected function outputReal()
    {
        $dealer = $this->dealer;
        $year_real = 0;

        if ($dealer) {
            $real = new RealBudgetCalculator($dealer, $this->year);

            $this->real = $real->calculate();
            foreach ($this->real as $k => $item)
                $year_real += $item;
        }

        $this->year_real = $year_real;
    }

    protected function outputMonths()
    {
        $this->months = array(
            1 => array('Январь', 'Февраль', 'Март'),
            2 => array('Апрель', 'Май', 'Июнь'),
            3 => array('Июль', 'Август', 'Сентябрь'),
            4 => array('Октябрь', 'Ноябрь', 'Декабрь'),
        );
    }

    protected function outputCurrentQuarter()
    {
        $this->current_quarter = D::getQuarter(time());

        if (date('d') < 9 && date('Y') == 2014)
            $this->current_quarter -= 1;
    }

    protected function outputQuarterEnds()
    {
        $this->quarter_ends = array(
            1 => 31,
            2 => 30,
            3 => 30,
            4 => 31
        );
    }

    protected function outputQuarterDays()
    {
        $quarter_days = array();

        $cur_date = getdate();
        for ($n = 1; $n <= 4; $n++) {
            $start_month = ($n - 1) * 3 + 1;
            $end_month = $start_month + 2;
            $start_date = getdate(mktime(12, 0, 0, $start_month, 1, $this->year));
            $end_date = getdate(mktime(12, 0, 0, $end_month + 1, 0, $this->year));
            $quarter_length = $end_date['yday'] - $start_date['yday'] + 1;
            $quarter_day = $cur_date['yday'] - $start_date['yday'];
            if ($quarter_day < 0)
                $quarter_day = 0;
            elseif ($cur_date['yday'] > $end_date['yday'])
                $quarter_day = $quarter_length - 1;

            $quarter_days[$n] = array(
                'length' => $quarter_length,
                'day' => $quarter_day
            );
        }

        $this->quarter_days = $quarter_days;
    }

    protected function outputAcceptStat()
    {
        $full_stat = array(
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array()
        );

        if ($this->dealer) {
            $full_stat = ActivityTable::getInstance()->getAcceptStat($this->year, $this->dealer);
        }
        /*$view_stat = array(
          1 => 0,
          2 => 0,
          3 => 0,
          4 => 0
        );*/

        /*for($q = 1; $q <= 4; $q ++)
        {
          $view_stat[$q] += $full_stat[$q];

          if($view_stat[$q] > 3)
          {
            $overflow = $view_stat[$q] - 3;
            $view_stat[$q] = 3;
            /*if($q < 4)
              $view_stat[$q + 1] += $overflow;*/

        /*}
      }*/

        $this->accept_stat = $full_stat;
    }

}
