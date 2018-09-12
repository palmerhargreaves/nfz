<?php

/**
 * Description of AgreementActivityStatusUtils
 *
 * @author Сергей
 */
class AgreementActivityStatusUtils
{
    /**
     * An activity
     *
     * @var Activity
     */
    protected $activity;
    /**
     * A dealer
     *
     * @var Dealer
     */
    protected $dealer;

    function __construct(Activity $activity, Dealer $dealer)
    {
        $this->activity = $activity;
        $this->dealer = $dealer;
    }

    /**
     * Returns status of an activity
     *
     * @return int
     */
    function getStatus()
    {
        if ($this->activity->getIsOwn())
            return AgreementActivityModuleDescriptor::STATUS_NONE;

        $result = Doctrine_Query::create()
            ->select(
                'count(*) total, 
          			 count(if(am.status="declined" or amr.status="declined",am.id,null)) declined, 
          			 count(if(wait<>0 or wait_specialist<>0,id,null)) wait, 
          			 count(if(am.status="accepted" and am.report_id is not null and am.wait=0,am.id,null)) tAccepted,
                 count(if(am.status="accepted" and amr.status="accepted",am.id,null)) allAccepted'
            )
            ->from('AgreementModel am')
            ->leftJoin('am.AgreementModelReport amr')
            ->where('am.activity_id=? and am.dealer_id=?', array($this->activity->getId(), $this->dealer->getId()))
            ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

        if ($result['allAccepted'] > 0)
            return AgreementActivityModuleDescriptor::STATUS_ACCEPTED;

        if ($result['declined'] > 0)
            return AgreementActivityModuleDescriptor::STATUS_WAIT_DEALER;

        if ($result['wait'] > 0)
            return AgreementActivityModuleDescriptor::STATUS_WAIT_AGREEMENT;

        /*if(($result['tAccepted'] > 0) && ($result['tAccepted'] == $result['total']))
            return AgreementActivityModuleDescriptor::STATUS_ACCEPTED;*/

        if ($this->activity->isAcceptedForDealer($this->dealer))
            return AgreementActivityModuleDescriptor::STATUS_ACCEPTED;


        return AgreementActivityModuleDescriptor::STATUS_NONE;
    }

    function updateActivityAcceptance()
    {
        if (AgreementModelBlankTable::getInstance()->hasActivityBlanks($this->activity)) {
            if (AgreementModelBlankTable::getInstance()->didAllBlanks($this->activity, $this->dealer))
                $this->activity->acceptForDealer($this->dealer, $this->calcAcceptDate());
            else
                $this->activity->declineForDealer($this->dealer);
        } else {
            if ($this->activity->isAllTasksDone($this->dealer))
                $this->activity->acceptForDealer($this->dealer, $this->calcAcceptDate());
            else
                $this->activity->declineForDealer($this->dealer);
        }
    }

    protected function calcAcceptDate()
    {
        $query = AgreementModelReportTable::getInstance()
            ->createQuery('r')
            ->select('r.*')
            ->innerJoin('r.Model m WITH m.activity_id=? and m.dealer_id=?', array($this->activity->getId(), $this->dealer->getId()))
            ->where('r.status=?', 'accepted')
            ->orderBy('r.created_at desc')
            ->limit(1);

        if (AgreementModelBlankTable::getInstance()->hasActivityBlanks($this->activity))
            $query->innerJoin('m.Blank');

        $last_created = $query->fetchOne();

        if (!$last_created || !$last_created->created_at)
            return 0;

        $date = D::toUnix($last_created->created_at);


        // Чтобы отчёты, отправленные до 5-го числа следующего месяца, попали в
        // предыдущий квартал, вычитаем из даты размещения отчёта 5 дней.
        // Январь - исключение (вычитаем 20 дней).
        $diff_days = date('n', $date) == 1 ? 20 : 5;

        return strtotime('-' . $diff_days . ' days', $date);
    }


}
