<?php

require_once dirname(__FILE__) . '/../lib/materialGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/materialGeneratorHelper.class.php';


ini_set('memory_limit', '1000M');
/*ini_set('upload_max_filesize', '1500M');
ini_set('post_max_size', '1500M');

set_time_limit(60 * 60);*/

/**
 * material actions.
 *
 * @package    Servicepool2.0
 * @subpackage material
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class materialActions extends autoMaterialActions
{
    protected $action;
    private $_materials = array();

    private static $_query = '';

    const FILTER_NAMESPACE = 'downloads';

    public function preExecute()
    {
        $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
        $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));

        parent::preExecute();
    }

    public function executeCreate(sfWebRequest $request)
    {
        $this->action = 'add';

        parent::executeCreate($request);
    }

    public function executeUpdate(sfWebRequest $request)
    {
        $this->action = 'edit';

        parent::executeUpdate($request);
    }

    protected function buildQuery()
    {
        $query = parent::buildQuery();
        $query->orderBy('r.material_order DESC');

        $mats = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        $materials = array();

        $this->_materials = array();
        for ($i = 0; $i < count($mats); $i++) {
            $materials[$i] = array('material_order' => $mats[$i]['material_order'], 'id' => $mats[$i]['id'], 'material_status' => $mats[$i]['material_status']);
        }

        $this->getUser()->setAttribute('materials', $materials);

        $query = parent::buildQuery();

        return $query;
    }

    protected function addSortQuery($query) {
        $sort = $this->getSort();

        if (array(null, null) == ($sort)) {
            return ;
        }

        if (!in_array(strtolower($sort[1]), array('asc', 'desc'))) {
            $sort[1] = 'asc';
        }

        switch($sort[0]) {
            case 'id':
                $sort[0] = 'id';
                break;
        }

        $query->addOrderBy($sort[0] . ' ' . $sort[1]);

        return $query;
    }

    protected function isValidSortColumn($column) {
        return Doctrine_Core::getTable('Material')->hasColumn($column);
    }

    protected function addToLog($action, $object)
    {
//    $description = '';
//    if($action == 'add')
//      $description = 'Добавлен';
//    elseif($action == 'edit')
//      $description = 'Изменён';
//    elseif($action == 'delete')
//      $description = 'Удален';
//    
//    LogEntryTable::getInstance()->addEntry(
//      $this->getUser()->getAuthUser(), 
//      'material', 
//      $action, 
//      'Материал/'.$object->getName(), 
//      $description, 
//      $action != 'delete' ? 'clip' : '', 
//      null, 
//      $object->getId(),
//      'materials'
//    );
    }

    public function onSaveObject(sfEvent $event)
    {
        $this->addToLog($this->action, $event['object']);

        $this->saveSources($event['object']);
        $this->saveWebPreviews($event['object']);
    }

    public function onDeleteObject(sfEvent $event)
    {
        $this->addToLog('delete', $event['object']);
    }

    protected function saveSources(Material $material)
    {
        foreach ($this->form->getValue('source') as $file) {
            if (!$file)
                continue;

            $source = new MaterialSource();
            $source->setMaterial($material);
            $source->setFromServerFile($file);
            $source->setName('Исходник');
            $source->save();
        }
    }

    protected function saveWebPreviews(Material $material)
    {
        foreach ($this->form->getValue('web_preview') as $file) {
            if (!$file)
                continue;

            $preview = new MaterialWebPreview();
            $preview->setMaterial($material);
            $preview->setFromServerFile($file);
            $preview->save();
        }
    }

    public function executeReorderMaterials(sfWebRequest $request)
    {
        $materials = $request->getParameter('elements');

        foreach ($materials as $mat) {
            $material = MaterialTable::getInstance()->find($mat['id']);
            $material->setMaterialOrder($mat['position']);
            $material->save();
        }

        $this->redirect('material');
    }

    public function executeBatchCopy(sfWebRequest $request)
    {
        $ids = $request->getParameter('ids');
        $orders_pos = array();

        $items = MaterialTable::getInstance()->createQuery()->whereIn('id', $ids)->orderBy('material_order ASC')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($items as $item) {
            $item_id_temp = $item['id'];
            unset($item['id']);

            $item['created_at'] = date('Y-m-d H:i:s');
            $item['updated_at'] = date('Y-m-d H:i:s');
            $item['material_order'] = -999;

            $newItem = new Material();
            $newItem->setArray($item);
            $newItem->save();

            $material_activities = ActivityMaterialsTable::getInstance()->createQuery()->where('material_id = ?', $item_id_temp)->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            foreach ($material_activities as $material_activity) {
                unset($material_activity['id']);

                $material_activity['material_id'] = $newItem->getId();

                $new_mat_activity = new ActivityMaterials();
                $new_mat_activity->setArray($material_activity);;
                $new_mat_activity->save();
            }
        }

        $this->getUser()->setFlash('notice', 'The selected items have been copied successfully.');
        $this->redirect('material');
    }

    public function executeDownloads(sfWebRequest $request)
    {
        $this->outputFilters();

        $query = MaterialDownloadsTable::getInstance()->createQuery()->select()->groupBy('material_id');

        $startDateFilter = $this->getStartDateFilter();
        if (!empty($startDateFilter))
            $query->andWhere('created_at >= ?', D::toDb($startDateFilter));

        $endDateFilter = $this->getEndDateFilter();
        if (!empty($endDateFilter))
            $query->andWhere('created_at <= ?', D::toDb($endDateFilter));

        $this->materials = $query->execute();
    }

    function executeMaterialClearDownloadFilters(sfWebRequest $request)
    {
        $this->getUser()->setAttribute('dealer_id', 0, self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('start_date', '', self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('end_date', '', self::FILTER_NAMESPACE);

        $this->redirect('material/downloads');
    }

    function outputFilters()
    {
        $this->startDateFilter = $this->getStartDateFilter();
        $this->endDateFilter = $this->getEndDateFilter();
    }

    function getStartDateFilter()
    {
        $default = $this->getUser()->getAttribute('start_date', '', self::FILTER_NAMESPACE);
        $startDate = $this->getRequestParameter('start_date', $default);

        $this->getUser()->setAttribute('start_date', $startDate, self::FILTER_NAMESPACE);

        return $startDate;
    }

    function getEndDateFilter()
    {
        $default = $this->getUser()->getAttribute('end_date', '', self::FILTER_NAMESPACE);
        $endDate = $this->getRequestParameter('end_date', $default);

        $this->getUser()->setAttribute('end_date', $endDate, self::FILTER_NAMESPACE);

        return $endDate;
    }

    public function executeStatus(sfWebRequest $request) {
        $material = MaterialTable::getInstance()->find($request->getParameter('material_id'));
        $material_status = intval($request->getParameter('material_status')) == 1 ? true : false;

        if ($material) {
            $material->setMaterialStatus($material_status);
            $material->save();
        }

        return sfView::NONE;
    }
}
