<?php

/**
 * Description of components
 *
 * @author Сергей
 */
class budgetComponents extends sfComponents
{
  function executeBudgetPanel()
  {
    $this->year = date('Y');
    
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
    $year = date('Y');
    $year_plan = 0;
    
    $defined_plan = BudgetTable::getInstance()
                    ->createQuery()
                    ->where(
                      'dealer_id=? and year=?', 
                      array($dealer->getId(), $year)
                    )
                    ->orderBy('quarter asc')
                    ->execute();
    
    $plan = array();
    for($n = 1; $n <= 4; $n ++)
    {
      $empty_plan = new Budget();
      $empty_plan->setArray(array(
        'dealer_id' => $dealer->getId(),
        'year' => $year,
        'quarter' => $n,
        'plan' => 0
      ));
      $plan[$n] = $empty_plan;
    }
    
    foreach($defined_plan as $p)
    {
      $plan[$p->getQuarter()] = $p;
      $year_plan += $p->getPlan();
    }
    
    $this->plan = $plan;
    $this->year_plan = $year_plan;
  }
  
  protected function outputReal()
  {
    $real = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
	$isAllComp = array(1 => false, 2 => false, 3 => false, 4 => false);
	
    $dealer = $this->dealer;
    $year = date('Y');
    $year_real = 0;
    
    $query = RealTotalBudgetTable::getInstance()
             ->createQuery()
             ->where('dealer_id=? and year=?', array($dealer->getId(), $year));
    
	$models = AgreementModelTable::getInstance()
                    ->createQuery('m')
                    ->innerJoin('m.Activity a')
                    ->innerJoin('m.ModelType mt')
                    ->leftJoin('m.Report r')
                    ->leftJoin('m.Discussion d')
                    ->where('m.dealer_id=?', $dealer->getId())
                    ->orderBy('m.id desc')
                    ->execute();
					
    foreach($query->execute() as $budget)
    {
      $real[$budget->getQuarter()] = $budget->getSum();
		/*foreach($models as $model) {
			$quarter = D::getQuarter($model->created_at);
			
			if($budget->getQuarter() == $quarter) {
				if($model->getReportCssStatus() == 'ok') {
					$real[$budget->getQuarter()] += $model->getCost();
					$year_real += $model->getCost();
				}
			}
		}*/
		
		$year_real += $budget->getSum();
    }
    
    $this->real = $real;
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
    $cur_quarter = D::getQuarter(time());
    $year = date('Y');
    
    $cur_date = getdate();
    for($n = 1; $n <= 4; $n ++)
    {
      $start_month = ($n - 1) * 3 + 1;
      $end_month = $start_month + 2;
      $start_date = getdate(mktime(12, 0, 0, $start_month, 1, $year));
      $end_date = getdate(mktime(12, 0, 0, $end_month + 1, 0, $year));
      $quarter_length = $end_date['yday'] - $start_date['yday'] + 1;
      $quarter_day = $cur_date['yday'] - $start_date['yday'];
      if($quarter_day < 0)
        $quarter_day = 0;
      elseif($cur_date['yday'] > $end_date['yday'])
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
    $full_stat = ActivityTable::getInstance()->getAcceptStat($this->year, $this->dealer);
    $view_stat = array(
      1 => 0,
      2 => 0,
      3 => 0,
      4 => 0
    );
    
    for($q = 1; $q <= 4; $q ++)
    {
      $view_stat[$q] += $full_stat[$q];
      if($view_stat[$q] > 3)
      {
        $overflow = $view_stat[$q] - 3;
        $view_stat[$q] = 3;
        if($q < 4)
          $view_stat[$q + 1] += $overflow;
      }
    }
    
    $this->accept_stat = $view_stat;
  }
}
