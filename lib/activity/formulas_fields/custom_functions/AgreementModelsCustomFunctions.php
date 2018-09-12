<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.08.2016
 * Time: 18:06
 */
class AgreementModelsCustomFunction
{
    private $_activity_id = 0;
    private $_dealer_id = 0;

    public function __construct($activity_id, $dealer_id)
    {
        $this->_activity_id = $activity_id;
        $this->_dealer_id = $dealer_id;
    }

    public function getFormulaModelsCount()
    {

    }

    public function getModelsAcceptedSumm()
    {
        $result = AgreementModelTable::getInstance()
            ->createQuery('am')
            ->select('sum(am.cost) as models_cash')
            ->leftJoin('am.Report r')
            ->where('am.activity_id = ? and am.dealer_id = ?', array($this->_activity_id, $this->_dealer_id))
            ->andWhere('am.status = ? and r.status = ?', array('accepted', 'accepted'))
            ->fetchOne(arraY(), Doctrine_Core::HYDRATE_ARRAY);

        if ($result) {
            return $result['models_cash'];
        }

        return 0;
    }
}
