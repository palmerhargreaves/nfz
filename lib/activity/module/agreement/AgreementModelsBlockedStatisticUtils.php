<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 19.04.2016
 * Time: 10:50
 */
class AgreementModelsBlockedStatisticUtils
{
    private $_blocked_models = array('models' => array(), 'total_blocked_models' => 0);

    public function __construct($filter = array())
    {
        $this->loadData();
    }

    private function loadData()
    {
        $models = AgreementModelTable::getInstance()->createQuery('m')
            //->where('m.is_blocked = ?', false)
            ->select('m.id, m.name, m.is_blocked, m.allow_use_blocked, m.use_blocked_to, m.status, r.id, v.id, v.value, v.field_id, m.created_at')
            ->leftJoin('m.Report r')
            ->leftJoin('m.Values v')
            ->where('m.is_blocked = ?', true)
            ->orderBy('m.id ASC')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($models as $model) {
            $year = D::getYear($model['created_at']);

            $blocked_info = array('total_blocked_count' => 0, 'total_unblock_count' => 0, 'created_at' => '', 'updated_at' => '');
            $model_blocked_stat = AgreementModelsBlokedStatisticsTable::getInstance()->createQuery()->where('model_id = ?', $model['id'])->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
            if ($model_blocked_stat) {
                $model_blocked_stat_items = AgreementModelsBlokedStatisticsItemsTable::getInstance()->createQuery()->where('parent_id = ?', $model_blocked_stat['id'])->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

                foreach ($model_blocked_stat_items as $item) {
                    if ($item['type'] == 'active') {
                        $blocked_info['total_unblock_count']++;
                    } else {
                        $blocked_info['total_blocked_count']++;
                    }
                }

                $blocked_info['created_at'] = $model_blocked_stat['created_at'];
                $blocked_info['updated_at'] = $model_blocked_stat['updated_at'];
            }

            $this->_blocked_models['models'][$year][] = array('model' => $model, 'blocked_info' => $blocked_info);
            $this->_blocked_models['total_blocked_models']++;
        }
    }

    public function getData()
    {
        return $this->_blocked_models;
    }
}