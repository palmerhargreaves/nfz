<?php

/**
 * Description of MaterialsHistoryProcessor
 *
 * @author Сергей
 */
class MaterialsHistoryProcessor implements HistoryProcessor
{
    function getSourceUri(LogEntry $entry)
    {
        switch ($entry->getObjectType()) {
            case 'material':
                $params = $this->getMaterialUriParams($entry->getObjectId());
                return $params ? $this->getMaterialUri($entry, $params) : false;


            case 'material_category':
                return $this->getMaterialUri($entry, 'material/' . $entry->getObjectId());


            case 'material_source':
                $source = MaterialSourceTable::getInstance()->find($entry->getObjectId());
                if (!$source)
                    return false;

                $params = $this->getMaterialUriParams($source->getMaterialId());
                return $params ? $this->getMaterialUri($entry, $params) : false;


            case 'material_preview':
                $preview = MaterialWebPreviewTable::getInstance()->find($entry->getObjectId());
                if (!$preview)
                    return false;

                $params = $this->getMaterialUriParams($preview->getMaterialId());
                return $params ? $this->getMaterialUri($entry, $params) : false;
        }

        return false;
    }

    public function getModelNumber(LogEntry $entry)
    {
        return 0;
    }

    protected function getMaterialUri(LogEntry $entry, $params)
    {
        $material_query = MaterialTable::getInstance()->createQuery('m');

        if ($entry->getObjectType() == 'material')
            $material_query->andWhere('id=?', $entry->getObjectId());
        elseif ($entry->getObjectType() == 'material_category')
            $material_query->andWhere('category_id=?', $entry->getObjectId())->limit(1);
        elseif ($entry->getObjectType() == 'material_source')
            $material_query->innerJoin('m.Sources ms WITH ms.id=?', $entry->getObjectId());
        elseif ($entry->getObjectType() == 'material_preview')
            $material_query->innerJoin('m.WebPreviews mp WITH mp.id=?', $entry->getObjectId());

        $material = $material_query->fetchOne();
        if (!$material)
            return false;

        $activity_id = $material->getActivityId();
        if (!$activity_id) {
            $activity = ActivityTable::getInstance()
                ->createQuery('a')
                ->select('a.id')
                ->innerJoin('a.Modules m WITH identifier=?', 'materials')
                ->limit(1)
                ->fetchOne();

            $activity_id = $activity ? $activity->getId() : false;
        }

        return $activity_id ? '@activity_materials?activity=' . $activity_id . '#' . $params : false;
    }

    /**
     * Returns a material category
     *
     * @param int $material_id
     * @return MaterialCategory|null
     */
    protected function getMaterialCategory($material_id)
    {
        $material = MaterialTable::getInstance()->find($material_id);
        return $material ? $material->getCategory() : null;
    }

    protected function getMaterialUriParams($material_id)
    {
        $category = $this->getMaterialCategory($material_id);
        return $category ? 'material/' . $category->getId() . '/' . $material_id : false;
    }
}
