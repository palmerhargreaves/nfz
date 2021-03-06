<?php

/**
 * RealBudgetTable
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RealBudgetTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return RealBudgetTable
     */
    static function getInstance()
    {
        return Doctrine_Core::getTable('RealBudget');
    }

    function addByReportDate(Dealer $dealer, $sum, ActivityModule $module, $date, $object_id = 0)
    {
        $date = D::toUnix($date);

        // Чтобы отчёты, отправленные до 8-го числа следующего месяца, попали в
        // предыдущий квартал, вычитаем из даты размещения отчёта 8 дней.
        // Январь - исключение (вычитаем 20 дней).
        $diff_days = date('n', $date) == 1 ? 20 : 8;
        $date = strtotime('-' . $diff_days . ' days', $date);

        $this->add($dealer, $sum, $module, date('Y', D::toUnix($date)), D::getQuarter($date), $object_id);
    }

    function add(Dealer $dealer, $sum, ActivityModule $module, $year = false, $quarter = false, $object_id = 0)
    {
        if (!$year)
            $year = date('Y');
        if (!$quarter)
            $quarter = D::getQuarter(time());

        $this->remove($dealer, $module, $year, $quarter, $object_id);

        $has_manualy_budget = $this->createQuery()
                ->where(
                    'dealer_id=? and year=? and quarter=? and module_id=? and object_id=?',
                    array($dealer->getId(), $year, $quarter, $module->getId(), 0)
                )->count() > 0;

        // если дилер в данном году и квартале имеет вручную установленные бюджет,
        // то пропускаем добавление суммы
        if (!$has_manualy_budget) {
            $real = new RealBudget();
            $real->setArray(array(
                'dealer_id' => $dealer->getId(),
                'year' => $year,
                'quarter' => $quarter,
                'sum' => $sum,
                'module_id' => $module->getId(),
                'object_id' => $object_id
            ));
            $real->save();
        }

        RealTotalBudgetTable::getInstance()->recalculate($dealer, $year);
    }

    function remove(Dealer $dealer, ActivityModule $module, $year = false, $quarter = false, $object_id = 0)
    {
        if (!$year)
            $year = date('Y');
        if (!$quarter)
            $quarter = D::getQuarter(time());

        $this->createQuery()
            ->delete()
            ->where(
                'dealer_id=? and year=? and quarter=? and module_id=? and object_id=?',
                array($dealer->getId(), $year, $quarter, $module->getId(), $object_id)
            )
            ->execute();

        RealTotalBudgetTable::getInstance()->recalculate($dealer, $year);
    }

    function removeByObjectOnly(ActivityModule $module, $object_id = 0)
    {
        $budget = $this->createQuery()
            ->where('module_id=? and object_id=?', array($module->getId(), $object_id))
            ->fetchOne();

        if ($budget) {
            $budget->delete();
            RealTotalBudgetTable::getInstance()->recalculate($budget->getDealer(), $budget->getYear());
        }
    }
}
