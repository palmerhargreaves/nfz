<?php

include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel.php');
include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel/Cell.php');
include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel/Writer/Excel5.php');

require_once dirname(__FILE__) . '/../lib/activityGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/activityGeneratorHelper.class.php';

/**
 * activity actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activityActions extends autoActivityActions
{
    protected $action;

    private $_showAll = false;

    public function preExecute()
    {
        $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
        $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));

        $request = $this->getRequest();
        $this->_showAll = true;

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
        $ind = 1;
        $query = parent::buildQuery();

        $query->orderBy('id DESC');
        $items = $query->execute();
        foreach ($items as $item) {
            if ($item->getPosition() == -999) {
                $item->setPosition($ind);
                $item->save();

                $ind++;
            }
        }

        $query = parent::buildQuery();
        return $query->orderBy('position ASC');
    }

    protected function addToLog($action, $object)
    {
        $description = '';
        if ($action == 'add') {
            $description = 'Добавлена';

            if (!$object->getHide()) {
                UserTable::getInstance()->callWithDealer(function (User $user) use ($object) {
                    if($user->getAllowReceiveMails()) {
                        $message = new ActivityCreateMail($user, $object);
                        $message->setPriority(1);

                        sfContext::getInstance()->getMailer()->send($message);
                    }
                });
            }
        } elseif ($action == 'edit') {
            $description = 'Изменена';

            /*if(!$object->getHide()) {

              UserTable::getInstance()->callWithDealer(function(User $user) use($object) {
                  $message = new ActivityCreateMail($user, $object, false);
                  $message->setPriority(1);

                  sfContext::getInstance()->getMailer()->send($message);
                  exit;
              });
            }*/
        } elseif ($action == 'delete')
            $description = 'Удалена';

        $entry = LogEntryTable::getInstance()->addEntry($this->getUser()->getAuthUser(), 'activity', $action, $object->getName(), $description, '', null, $object->getId());
        $entry->setImportance(true);
        $entry->save();
    }

    public function onSaveObject(sfEvent $event)
    {
        $this->addToLog($this->action, $event['object']);
    }

    public function onDeleteObject(sfEvent $event)
    {
        $this->addToLog('delete', $event['object']);
    }

    public function executeBatchCopy(sfWebRequest $request)
    {
        $ids = $request->getParameter('ids');
        $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();

        foreach ($ids as $id) {
            $activity = ActivityTable::getInstance()->find($id);

            $newActivity = new Activity();

            $data = $activity->toArray();
            $data['id'] = null;

            $newActivity->setArray($data);
            $newActivity->save();

            /*foreach ($activity->getMaterials() as $material) {
                $matData = $material->toArray();
                $matData['id'] = null;
                $matData['activity_id'] = $newActivity->getId();

                $newMaterial = new Material();
                $newMaterial->setArray($matData);
                $newMaterial->save();

                foreach ($material->getSources() as $source) {
                    $sourceData = $source->toArray();
                    $sourceData['id'] = null;
                    $sourceData['material_id'] = $newMaterial->getId();

                    $newMatSource = new MaterialSource();
                    $newMatSource->setArray($sourceData);
                    $newMatSource->save();
                }

                foreach ($material->getWebPreviews() as $web) {
                    $query = "INSERT INTO material_web_preview(material_id, file, created_at, updated_at) VALUES(:param1, :param2, :param3, :param4)";
                    $smt = $pdo->prepare($query);

                    $smt->execute(array('param1' => $newMaterial->getId(),
                        'param2' => $web->getFile(),
                        'param3' => date('Y-m-d H:i:s'),
                        'param4' => date('Y-m-d H:i:s')));
                }
            }

            foreach ($activity->getFiles() as $file) {
                $fileData = $file->toArray();
                $fileData['id'] = null;
                $fileData['activity_id'] = $newActivity->getId();

                $newFile = new ActivityFile();
                $newFile->setArray($fileData);
                $newFile->save();
            }*/

            foreach ($activity->getTasks() as $task) {
                $taskData = $task->toArray();
                $taskData['id'] = null;
                $taskData['activity_id'] = $newActivity->getId();

                $newTask = new ActivityTask();
                $newTask->setArray($taskData);
                $newTask->save();
            }

            $modules = AcivityModuleActivityTable::getInstance()->createQuery()->where('activity_id = ?', $activity->getId())->execute();
            foreach ($modules as $module) {
                $query = "INSERT INTO acivity_module_activity(activity_id, module_id) VALUES(:param1, :param2)";
                $smt = $pdo->prepare($query);

                $smt->execute(array('param1' => $newActivity->getId(),
                    'param2' => $module->getModuleId()));
            }
        }

    }

    function executeReorder(sfWebRequest $request)
    {
        $data = json_decode($request->getParameter('data'));
        $items = array();

        $ind = 1;
        foreach ($data->{'activities-list'} as $key) {
            if (!empty($key) && is_numeric($key)) {
                $activity = ActivityTable::getInstance()->find($key);
                if ($activity) {
                    $activity->setPosition($ind);
                    $activity->save();
                }

                $ind++;
            }
        }

        return sfView::NONE;
    }

    function executeFieldsList(sfWebRequest $request)
    {
        $this->activityId = $request->getParameter('activity_id');
        $this->fields = ActivityInfoFieldsTable::getInstance()
            ->createQuery()->select()->orderBy('id ASC')
            ->execute();
    }

    function executeFieldData(sfWebRequest $request)
    {
        $this->activityId = $request->getParameter('id');
        $this->fieldId = $request->getParameter('fieldId');

        $this->items = ActivityInfoFieldsDataTable::getInstance()
            ->createQuery()
            ->select()
            ->where('activity_id = ? and field_id = ?',
                array($request->getParameter('id'), $request->getParameter('fieldId')))
            ->execute();
    }

    function executeFieldsAcceptData(sfWebRequest $request)
    {
        $valid = false;
        $data = $request->getParameter('data');

        foreach ($data as $key => $item) {
            $msg = trim($item['msg']);

            if (strlen($msg) > 0) {
                $isNew = (bool)$item['isNew'];

                $fieldData = $isNew ? new ActivityInfoFieldsData() : ActivityInfoFieldsDataTable::getInstance()->find((int)$item['id']);

                if ($isNew) {
                    $fieldData->setActivityId($item['activityId']);
                    $fieldData->setFieldId($item['fieldId']);
                }

                $fieldData->setDescription($item['msg']);
                $fieldData->save();

                $valid = true;
            }
        }

        $this->valid = $valid;
    }

    function executeFieldsDataDelete(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $valid = false;

        $fieldData = ActivityInfoFieldsDataTable::getInstance()->find($id);
        if ($fieldData) {
            $fieldData->delete();
            $valid = true;
        }

        $this->valid = $valid;
    }

    public function executeExportActivityDealers(sfWebRequest $request)
    {
        $result = AgreementModelTable::getInstance()
            ->createQuery()
            ->where('activity_id = ?', $request->getParameter('activity'))
            ->groupBy('dealer_id')
            ->execute();

        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle('Dealers list');

        $headers = array('Дилер (номер)', 'Дилер (название)', 'Заявок');
        $boldFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => true
            )
        );
        $center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
            )
        );

        $aSheet->getStyle('A1:G1')->applyFromArray($boldFont);
        $aSheet->getStyle('B:G')->applyFromArray($center);

        $column = 0;
        $tCount = 1;
        foreach ($headers as $head) {
            $aSheet->setCellValueByColumnAndRow($column++, 1, $head);
            $tCount++;
        }

        $aSheet->getColumnDimension('A')->setWidth(30);
        $aSheet->getColumnDimension('B')->setWidth(50);

        $row = 2;
        $tCount = 1;
        foreach ($result as $model) {
            $column = 0;
            $dealer = $model->getDealer();

            $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer->getNumber());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer->getName());

            $modelsCount = AgreementModelTable::getInstance()
                ->createQuery()
                ->where('activity_id = ? and dealer_id = ?', array($request->getParameter('activity'), $model->getDealerId()))
                ->count();

            $aSheet->setCellValueByColumnAndRow($column++, $row, $modelsCount);

            $aSheet->getStyle('A' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('B' . $tCount)->applyFromArray($center);

            $row++;
        }

        $aSheet->getStyle(
            'A2:' .
            $pExcel->getActiveSheet()->getHighestColumn() .
            $pExcel->getActiveSheet()->getHighestRow()
        )->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save(sfConfig::get('sf_root_dir') . '/www/uploads/activity_dealers.xls');

        $this->redirect('http://dm.vw-servicepool.ru/uploads/activity_dealers.xls');
    }

    public function executeDownloadFile(sfWebRequest $request)
    {
        $file = $request->getParameter('file');

        $filePath = sfConfig::get('app_uploads_path') . '/' . ActivityFile::FILE_PATH . '/' . $file;
        if (file_exists($filePath)) {
            $file = end(explode('/', $filePath));

            if (!F::downloadFile($filePath, $file)) {
                $this->getResponse()->setContentType('application/json');
                $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
            }
        }

        return sfView::NONE;
    }
}
