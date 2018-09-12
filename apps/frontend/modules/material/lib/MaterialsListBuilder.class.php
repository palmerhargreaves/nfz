<?php

/**
 * Description of MaterialsListBuilder
 *
 * @author Сергей
 */
class MaterialsListBuilder
{
    /**
     * Activity
     *
     * @var Activity
     */
    protected $activity;
    protected $materials = array();
    protected $views = array();

    function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    function build(User $user = null)
    {
        $this->materials = array();

        $this->loadActivityMaterials();
        //$this->loadCommonMaterials();

        if ($user)
            $this->loadViews($user);

        return $this->materials;
    }

    function getMaterials()
    {
        return $this->materials;
    }

    function isViewed($material_id)
    {
        return isset($this->views[$material_id]);
    }

    protected function loadActivityMaterials()
    {
        $materials = ActivityMaterialsTable::getInstance()
            ->createQuery()
            ->select('material_id')
            ->where('activity_id=?', $this->activity->getId())
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $materials = array_map(function($item) {
            return $item['material_id'];
        }, $materials);

        $materials = MaterialTable::getInstance()
            ->createQuery('m')
            ->select('m.*, mc.id, mc.name')
            ->innerJoin('m.Category mc')
            ->whereIn('m.id', $materials)
            ->andWhere('m.material_status = ?', true)
            ->orderBy('mc.name, m.material_order DESC, m.name')
            ->execute();

        /*$materials = MaterialTable::getInstance()
            ->createQuery('m')
            ->select('m.*, mc.id, mc.name')
            ->innerJoin('m.Category mc')
            ->where('m.activity_id=?', $this->activity->getId())
            ->orderBy('mc.name, m.material_order DESC, m.name')
            ->execute();*/

        $this->addMaterials($materials);
    }

    protected function loadCommonMaterials()
    {
        $materials = MaterialTable::getInstance()
            ->createQuery('m')
            ->select('m.*, mc.id, mc.name')
            ->innerJoin('m.Category mc')
            ->leftJoin('m.Activity a')
            ->where('a.id is null')
            ->andWhere('m.material_status = ?', true)
            ->orderBy('mc.name, m.material_order DESC, m.name')
            ->execute();

        $this->addMaterials($materials);
    }

    protected function addMaterials($materials)
    {
        $mats = array();
        $matsRes = MaterialCategoryTable::getInstance()
            ->createQuery()
            ->select()
            ->orderBy('category_order DESC')
            ->execute();

        foreach ($matsRes as $item) {
            if (!isset($this->materials[$item->getId()]))
                $this->materials[$item->getId()] = array(
                    'category' => $item->getName(),
                    'materials' => array()
                );
        }

        foreach ($materials as $material) {
            $category = $material->getCategory();
            if (isset($this->materials[$category->getId()])) {
                /*$this->materials[$category->getId()] = array(
                  'category' => $category->getName(),
                  'materials' => array()
                );*/
                $this->materials[$category->getId()]['materials'][] = $material;
            }

        }

        foreach ($this->materials as $key => $items) {
            if (count($items['materials']) == 0) {
                unset($this->materials[$key]);
            }
        }

    }

    protected function loadViews(User $user)
    {
        $materials = array();
        foreach ($this->materials as $category) {
            foreach ($category['materials'] as $material)
                $materials[] = $material->getId();
        }

        if (!$materials)
            return;

        $views = MaterialUserViewTable::getInstance()
            ->createQuery()
            ->where('user_id=?', $user->getId())
            ->andWhereIn('material_id', $materials)
            ->execute();

        foreach ($views as $view)
            $this->views[$view->getMaterialId()] = true;
    }
}
