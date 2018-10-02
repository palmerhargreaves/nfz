<?php

/**
 * agreement_activity_model actions.
 *
 * @package    Servicepool2.0
 * @subpackage agreement_activity_model
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agreement_activity_modelActions extends BaseActivityActions
{
    const SORT_ATTR = 'sort';
    const SORT_DIRECT_ATTR = 'sort_direct';
    const FILTER_NAMESPACE = 'agreement_filter';

    const MAX_FILES = 10;
    const CONCEPT_INDEX = 10;
    const GETTER = 'get';
    const SETTER = 'set';

    const REDACTOR_KEY = 'Ahtu9vee';

    private $uploaded_files_result = array();

    protected $check_for_module = 'agreement';

    /**
     * Executes index action
     *
     * @param sfWebRequest $request A request object
     */
    function executeIndex(sfWebRequest $request)
    {
        $this->year = $request->getParameter('year');

        $this->outputFilterByYear();
        $this->outputFilterByQuarter();
        $this->outputModelsQuarters($request);

        //$this->outputFilterByQuarter();
        //$this->outputModelByQ($request);\

        $this->outputActivity($request);
        $this->outputHasConcept($request);
        $this->outputConcept();
        $this->outputConceptType();

        $this->outputModelsQuarters($request);

        $this->outputModels($request);
        $this->outputBlanks($request);
        $this->outputModelTypes();
        $this->outputTaskList($request);
        $this->outputModelTypesFields();
        $this->outputActivities($request);

        $this->outputDealerFiles();

        $this->statisticQuarter = $request->getParameter('quarter', D::getQuarter(time()));
        $this->modelId = $request->getParameter('model');
    }

    function outputActivities(sfWebRequest $request)
    {
        $user = $this->getUser();
        $show_hidden = $user->isAdmin() || $user->isImporter() || $user->isManager();

        $query = ActivityTable::getInstance()
            ->createQuery('a')
            ->select('a.id, a.start_date, a.end_date, a.custom_date, a.name, a.brief, a.importance, v.id is_viewed')
            ->leftJoin('a.UserViews v WITH v.user_id=?', $this->getUser()->getAuthUser()->getId())
            ->orderBy('a.importance DESC, sort DESC, a.id DESC');

        if ($request->getParameter('year')) {
            $query->andWhere('a.start_date LIKE ?', $this->year . '%')
                ->andWhere('a.end_date LIKE ?', $this->year . '%');
        } else
            $query->where('a.finished=?', false);

        //if(!$show_hidden)
        $query->andWhere('a.hide=?', false);
        ActivityTable::checkActivity($user, $query);

        $this->activities = $query->execute();

    }

    function executeActivities(sfWebRequest $request)
    {
        $this->outputDealerModels($request);

        $this->outputFilter();

        //$this->executeIndex($request);

    }

    function outputDealerModels()
    {
        $sorts = array(
            'id' => 'm.id',
            'dealer' => 'm.dealer_id', // сортировка по id дилеров (фактически - это группировка)
            'name' => 'm.name',
            'cost' => 'm.cost'
        );

        $sort_column = $this->getSortColumn();
        $sort_direct = $this->getSortDirection();

        $sql_sort = 'm.id DESC';
        if (isset($sorts[$sort_column]))
            $sql_sort = $sorts[$sort_column] . ' ' . ($sort_direct ? 'DESC' : 'ASC');

        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activity a')
            ->innerJoin('m.ModelType mt WITH mt.concept=?', false)
            ->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r')
            ->orderBy($sql_sort);

        $dealer = $this->getUser()->getAuthUser()->getDealer();
        if (!$dealer) {
            $this->models = array();
        } else {

            $query->andWhere('m.dealer_id=?', $dealer->getId());
            if ($this->getActivityStatusFilter()) {
                switch ($this->getActivityStatusFilter()) {
                    case 'in_work':
                        $query->andWhere('m.wait_specialist = ? or m.status = ? or r.status = ?', array(1, 'wait', 'wait'));
                        break;

                    case 'complete':
                        $query->andWhere('m.status = ? and r.status=?', array('accepted', 'accepted'));
                        break;

                    case 'process_draft':
                        //$query->andWhere('m.status = ? or m.wait_specialist = ?', array('wait', 1));
                        $query->andWhere('m.status = ? or m.status = ?', array('declined', 'not_sent'))
                            ->andWhere('m.report_id is null');
                        break;

                    case 'process_reports':
                        $query->andWhere('m.status = ? and m.report_id is null', 'accepted');
                        break;

                    case 'all':
                        /*$query->andWhere('m.report_id is null')
                              ->andWhere('m.status = ?', 'accepted'); */
                        break;

                    case 'current':
                        $query->andWhere('m.status != ? or m.report_id is null', 'accepted');
                        break;
                }
            } else {
                /*$query->andWhere('m.status = ? or m.status = ? or m.status = ?', array('declined', 'not_sent', 'accepted'))
                      ->andWhere('m.report_id is null');/*/

                $query->andWhere('m.status != ?', 'accepted');
            }

            if ($this->getStartDateFilter()) {
                $query->andWhere('m.created_at >= ?', D::toDb($this->getStartDateFilter()));
            }

            if ($this->getEndDateFilter()) {
                $query->andWhere('m.created_at <= ?', D::toDb($this->getEndDateFilter()));
            }

            $by_year = D::getYear(D::calcQuarterData(time()));
            $this->models = $this->getModelsListByYear($query, $by_year);

        }
    }

    function getModelsListByYear($query, $by_year = null)
    {
        if (!is_null($by_year)) {
            $query->andWhere('(year(created_at) = ? or year(created_at) = ?)', array($by_year, $by_year - 1));
        }

        $this->models = $query->execute();

        $isDealer = $this->getUser()->isDealerUser();
        if ($this->getUser()->isImporter()) {
            $isDealer = false;
        }

        $mods = array();
        $result = array();

        $models_ids = array();
        $models_list = arraY();
        foreach ($this->models as $m) {
            $models_ids[] = $m->getId();
            $models_list[$m->getId()] = $m;
        }

        $models_log_dates = Utils::getModelDateFromLogEntryWithYear($models_ids);
        foreach ($models_log_dates as $model_date) {
            if (array_key_exists($model_date['object_id'], $models_list)) {
                $model = $models_list[$model_date['object_id']];
                $maked_date = $model->isCompleted() ? D::calcQuarterData($model_date['created_at']) : D::toUnix(D::makePlusDaysForModel($model, $model_date['created_at']));

                $mods[$maked_date] = array('model' => $model, 'model_date' => $model_date);
                unset($models_list[$model_date['object_id']]);
            }
        }

        $models_list = array_filter($models_list);
        foreach ($models_list as $key => $model) {
            if ($isDealer) {
                $maked_date = D::toUnix($m->getModelAcceptToDate($isDealer));
            } else {
                $maked_date = D::toUnix(D::makePlusDaysForModel($model, $model->getCreatedAt()));
            }

            $mods[$maked_date] = array('model' => $model, 'model_date' => array());
        }

        ksort($mods, SORT_NUMERIC);
        foreach ($mods as $key => $data) {
            $model = $data['model'];
            $model_label = date('H:i d-m-Y', $key);

            $end_time_work = $model->isOutOfDate();
            if ($this->getActivityStatusFilter() == 'blocked' && !$end_time_work) {
                continue;
            } else if(($this->getActivityStatusFilter() == 'blocked' || $this->getActivityStatusFilter() == 'default') && $end_time_work && !$model->getAllowUseBlocked()) {
                $model_label = "Заблокирована";
            } else if ($end_time_work) {
                continue;
            }

            $date = $model->isModelCompleted() ? $key : $model->getCreatedAt();

            $year = D::getYear($date);
            $prevYear = D::isPrevYear($date);

            $status = $model->isModelCompleted();
            $result[$year]['data'][] = array
            (
                'date' => $key,
                'model' => $model,
                'status' => $status ? $prevYear : false,
                'label' => $model_label,
                'end_time_work' => $end_time_work
            );

            if ($status) {
                $result[$year]['summ'] = isset($result[$year]['summ']) ? $result[$year]['summ'] + $model->getCost() : $model->getCost();
            }
        }

        return $result;
    }

    function outputActivitystatusFilter()
    {
        $this->activity_status = $this->getActivityStatusFilter();
    }

    function outputStartDateFilter()
    {
        $this->start_date_filter = $this->getStartDateFilter();
    }

    function outputEndDateFilter()
    {
        $this->end_date_filter = $this->getEndDateFilter();
    }


    function getActivityStatusFilter()
    {
        $default = $this->getUser()->getAttribute('activity_status', 'current', self::FILTER_NAMESPACE);
        $status = $this->getRequestParameter('activity_status', $default);
        $this->getUser()->setAttribute('activity_status', $status, self::FILTER_NAMESPACE);

        return $status;
    }

    function getStartDateFilter()
    {
        return $this->getDateFilter('start_date');
    }

    function getEndDateFilter()
    {
        return $this->getDateFilter('end_date');
    }

    protected function getDateFilter($name)
    {
        $default = $this->getUser()->getAttribute($name, '', self::FILTER_NAMESPACE);
        $date = $this->getRequestParameter($name, $default);
        $this->getUser()->setAttribute($name, $date, self::FILTER_NAMESPACE);

        return preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$#', $date)
            ? D::fromRus($date)
            : false;
    }

    function outputFilter()
    {
        $this->outputStartDateFilter();
        $this->outputEndDateFilter();

        $this->outputActivitystatusFilter();
    }

    function executeSort(sfWebRequest $request)
    {
        $column = $request->getParameter('sort', 'id');
        $cur_column = $this->getSortColumn();
        $direction = $this->getSortDirection();

        if ($column == $cur_column) {
            $direction = !$direction;
        } else {
            $direction = false;
            $cur_column = $column;
        }

        $this->setSortColumn($cur_column);
        $this->setSortDirection($direction);

        $this->redirect('@agreement_module_models?activity=' . $this->getActivity($request)->getId());
    }

    function newAdd(sfWebRequest $request) {

    }

    function executeAdd(sfWebRequest $request)
    {
        $draft = $request->getParameter('draft', 'false') == 'true';
        $blank_id = $request->getParameter('blank_id');
        $model_type_id = $request->getParameter('model_type_id');
        $task_id = $request->getParameter('task_id');

        if ($blank_id) {
            $blank = AgreementModelBlankTable::getInstance()->find($blank_id);
            // существование болванки проверит валидатор формы
            if ($blank)
                $model_type_id = $blank->getModelTypeId();
        }

        $no_model_changes = $request->getParameter('no_model_changes');
        $no_model_changes = is_null($no_model_changes) ? false : true;

        /*$model_accepted_in_online_redactor = $request->getParameter('model_accepted_in_online_redactor');
        $model_accepted_in_online_redactor = is_null($model_accepted_in_online_redactor) ? false : true;*/

        //return $this->sendJson(array($datesFields));

        //Тип заявки - Сценарий / Запись
        $is_model_scenario_record = ($model_type_id == 2 || $model_type_id == 4);

        $form = new AgreementModelForm($draft, null, array(), null, $this->getUser()->getAttribute('editor_link') ? true : false);

        $upload_files_ids = $request->getPostParameter('upload_files_ids');
        if (!$this->getUser()->getAttribute('editor_link')) {
            if ($no_model_changes && $is_model_scenario_record) {
                $upload_files_records_ids = $request->getPostParameter('upload_files_records_ids');

                if (empty($upload_files_ids)) {
                    $form->getValidator('is_valid_data')->setOption('required', true);
                }

                if (empty($upload_files_records_ids)) {
                    $form->getValidator('is_valid_data')->setOption('required', true);
                }
            } else {
                if (empty($upload_files_ids)) {
                    $form->getValidator('is_valid_data')->setOption('required', true);
                }
            }
        }

        $form->bind(
            array(
                'activity_id' => $this->getActivity($request)->getId(),
                'dealer_id' => $this->getUser()->getAuthUser()->getDealer()->getId(),
                'name' => $request->getParameter('name'),
                'blank_id' => $blank_id,
                'model_type_id' => $model_type_id,
                'task_id' => $task_id,
                'target' => $request->getParameter('target'),
                'cost' => $request->getParameter('cost'),
                'status' => $draft ? 'not_sent' : 'wait',
                'accept_in_model' => $request->getParameter('accept_in_model'),
                'no_model_changes' => $no_model_changes,
                //'model_accepted_in_online_redactor' => $model_accepted_in_online_redactor,
                'editor_link' => $this->getUser()->getAttribute('editor_link') ? $this->getUser()->getAttribute('editor_link') : ''
            ),
            array()
        //
        );

        $hasEditorLink = false;
        if ($form->isValid()) {
            $form->save();
            $model = $form->getObject();

            /**
             * Save uploaded files before check for model statuses
             */
            if (!$this->getUser()->getAttribute('editor_link')) {
                if (!$model->isModelScenario()) {
                    UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), $upload_files_ids, $this->getActivity($request)->getId())->saveFiles();
                } else if ($no_model_changes && $model->isModelScenario()) {
                    UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), $upload_files_ids, $this->getActivity($request)->getId(), 'Scenario')->saveFiles();
                    UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), $upload_files_records_ids, $this->getActivity($request)->getId(), 'Record')->saveFiles();
                } else if ($model->isModelScenario()) {
                    UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), $upload_files_ids, $this->getActivity($request)->getId(), 'ScenarioRecord')->saveFiles();
                }
            } else {
                UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), null, $this->getActivity($request)->getId())->saveFiles();
            }

            if ($this->getUser()->getAttribute('editor_link')) {
                $model->setModelFile($this->getUser()->getAttribute('editor_link'));
                $model->save();

                $hasEditorLink = true;
                $this->getUser()->setAttribute('editor_link', '');
            }

            if ($this->getActivity($request)->getAllowCertificate() && !$model->isConcept()) {
                $model->setConceptId($request->getParameter('concept_id'));
                $model->save();
            }

            if ($this->getActivity($request)->getAllowCertificate() && $model->isConcept()) {
                $this->addDatesPeriodAction($request, $model);
            }

            $this->updateModelValuesByType($model, $request);

            if (!$no_model_changes && ($model_type_id == 2 || $model_type_id == 4)) {
                $model->setStep1('wait');
                $model->save();
            }

            if (!$draft) {
                $utils = new AgreementActivityStatusUtils($model->getActivity(), $model->getDealer());
                $utils->updateActivityAcceptance();

                if ($model_type_id == 2 || $model_type_id == 4) {
                    $text = 'Сценарий отправлен на согласование.';
                } else {
                    $text = $model->isConcept() ? 'Концепция отправлена на согласование.' : 'Макет отправлен на согласование.';
                }
                $entry = LogEntryTable::getInstance()->addEntry(
                    $this->getUser()->getAuthUser(),
                    $model->isConcept() ? 'agreement_concept' : 'agreement_model',
                    'add',
                    $model->getActivity()->getName() . '/' . $model->getName(),
                    $text,
                    'clip',
                    $model->getDealer(),
                    $model->getId(),
                    'agreement'
                );

                $message = $this->addMessageToDiscussion($model, $text);

                //$this->attachFileToMessage($model, $message, $hasEditorLink);
                $this->attachFilesToMessage($model, $message, $form);

                AgreementManagementHistoryMailSender::send(
                    'AgreementSendModelMail',
                    $entry,
                    false,
                    false,
                    $model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
                );

                $this->setModelChanges($model, $model_type_id, $no_model_changes);
            } else {
                $statusLabel = $model->isConcept() ? 'Концепция отправлена как черновик.' : 'Макет отправлен как черновик.';
                if ($model_type_id == 2 || $model_type_id == 4) {
                    $statusLabel = 'Сценарий отправлен как черновик.';
                }
                $message = $this->addMessageToDiscussion($model, $statusLabel);

                //$this->attachFileToMessage($model, $message, $hasEditorLink);
                $this->attachFilesToMessage($model, $message);
            }
        }

        return $this->sendFormBindResult($form, 'agreement_model_form.onResponse', $hasEditorLink ? url_for('@agreement_module_models?activity=' . $this->getActivity($request)->getId()) : '');
    }

    function executeUpdate(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        if (!$model)
            return $this->sendJson(array('success' => false, 'error' => 'not_found'), 'agreement_model_form.onResponse');

        if ($model->getStep1() != 'accepted') {
            if ($model->getStatus() != 'not_sent' && $model->getStatus() != 'declined')
                return $this->sendJson(array('success' => false, 'error' => 'wrong_status'), 'agreement_model_form.onResponse');
        }

        $draft = $request->getParameter('draft', 'false') == 'true';

        $no_model_changes = $request->getParameter('no_model_changes');
        $no_model_changes = is_null($no_model_changes) ? false : true;

        /*$model_accepted_in_online_redactor = $request->getParameter('model_accepted_in_online_redactor');
        $model_accepted_in_online_redactor = is_null($model_accepted_in_online_redactor) ? false : true;*/

        $model_type_id = $model->getModelType()->getId();
        $blank_id = $request->getParameter('blank_id');
        if (!$blank_id) {
            $model_type_id = $request->getParameter('model_type_id');
        }

        $form = new AgreementModelForm($draft, $model);

        $uploaded_files = $model->getUploadedFilesCount($model_type_id);

        $upload_files_ids = $request->getPostParameter('upload_files_ids');
        $upload_files_records_ids = array();

        if ($no_model_changes && $model->isModelScenario($model_type_id)) {
            $upload_files_records_ids = $request->getPostParameter('upload_files_records_ids');

            if (empty($upload_files_ids) && $uploaded_files[AgreementModel::BY_SCENARIO] == 0) {
                $form->getValidator('is_valid_data')->setOption('required', true);
            }

            if (empty($upload_files_records_ids) && $uploaded_files[AgreementModel::BY_RECORD] == 0) {
                $form->getValidator('is_valid_data')->setOption('required', true);
            }
        } else {
            if ($model->isModelScenario($model_type_id)) {
                if ($model->getStep1() == 'accepted') {
                    $upload_files_ids = $request->getPostParameter('upload_files_records_ids');
                    if (empty($upload_files_ids) && $uploaded_files[AgreementModel::BY_RECORD] == 0) {
                        $form->getValidator('is_valid_data')->setOption('required', true);
                    }
                } else {
                    if (empty($upload_files_ids) && $uploaded_files[AgreementModel::BY_SCENARIO] == 0) {
                        $form->getValidator('is_valid_data')->setOption('required', true);
                    }
                }
            } else {
                if (empty($upload_files_ids) && $uploaded_files['model_file'] == 0) {
                    $form->getValidator('is_valid_data')->setOption('required', true);
                }
            }
        }

        $form->bind(
            array(
                //'activity_id' => $this->getActivity($request)->getId(),
                'activity_id' => $request->getParameter('activity_id'),
                'dealer_id' => $this->getUser()->getAuthUser()->getDealer()->getId(),
                'name' => $request->getParameter('name'),
                'blank_id' => $model->getBlankId(),
                'model_type_id' => $model_type_id,
                'task_id' => $model->getTaskId(),
                'target' => $request->getParameter('target'),
                'cost' => $request->getParameter('cost'),
                'status' => $draft ? 'not_sent' : 'wait',
                'accept_in_model' => $request->getParameter('accept_in_model'),
                'no_model_changes' => $no_model_changes,
                //'model_accepted_in_online_redactor' => $model_accepted_in_online_redactor
            ),
            array()//$this->getModelFiles($request, true)
        );

        if ($form->isValid()) {
            $form->save();
            $model = $form->getObject();

            $model->changeStepsStates();

            /*if (count($this->uploaded_files_result) > 0) {
                $this->saveModelFiles($model, $form, count($this->uploaded_files_result));
            }*/

            if (!$model->isModelScenario()) {
                $saved_files = UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), $upload_files_ids, $model->getActivityId())->saveFiles();
            }
            else if ($no_model_changes && $model->isModelScenario()) {
                $saved_scenario_files = UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), $upload_files_ids, $model->getActivityId(),'Scenario')->saveFiles();
                $saved_record_files = UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), $upload_files_records_ids, $model->getActivityId(),'Record')->saveFiles();

                $saved_files = array_merge($saved_scenario_files, $saved_record_files);
            } else if ($model->isModelScenario()) {
                $saved_files = UploadModelFilesFactory::getInstance()->createUpload($model, $this->getUser(), $upload_files_ids, $model->getActivityId(),'ScenarioRecord')->saveFiles();
            }

            $model->setManagerStatus('wait');
            $model->setDesignerStatus('wait');

            $model->setAgreementComments('');

            $modelType = AgreementModelTypeTable::getInstance()->find($model_type_id);
            $model->setModelType($modelType);
            $model->save();

            $this->updateModelValuesByType($model, $request);

            if ($this->getActivity($request)->getAllowCertificate() && !$model->isConcept()) {
                $model->setConceptId($request->getParameter('concept_id'));
                $model->save();
            }

            if ($this->getActivity($request)->getAllowCertificate() && $model->isConcept()) {
                $this->addDatesPeriodAction($request, $model);
            }

            if (!$draft) {
                $utils = new AgreementActivityStatusUtils($model->getActivity(), $model->getDealer());
                $utils->updateActivityAcceptance();

                if ($model_type_id == 2 || $model_type_id == 4) {
                    if ($model->getStep1() == "wait") {
                        $text = 'Сценарий отправлен на согласование.';
                    }
                    if ($model->getStep1() == "accepted" && $model->getStep2() == "wait") {
                        $text = 'Запись отправлена на согласование.';
                    }
                } else {
                    $text = $model->isConcept() ? 'Концепция отправлена на согласование.' : 'Макет отправлен на согласование.';
                }

                $entry = LogEntryTable::getInstance()->addEntry(
                    $this->getUser()->getAuthUser(),
                    $model->isConcept() ? 'agreement_concept' : 'agreement_model',
                    'edit',
                    $model->getActivity()->getName() . '/' . $model->getName(),
                    $text,
                    'clip',
                    $model->getDealer(),
                    $model->getId(),
                    'agreement'
                );

                $model->createPrivateLogEntryForSpecialists($entry);

                $message = $this->addMessageToDiscussion($model, $text);
                ///$this->attachFileToMessage($model, $message);
                $this->attachFilesToMessage($model, $message, $form, $saved_files);

                AgreementManagementHistoryMailSender::send(
                    'AgreementSendModelMail',
                    $entry,
                    false,
                    false,
                    $model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
                );

                $this->setModelChanges($model, $model_type_id, $no_model_changes);
            }

        }


        return $this->sendFormBindResult($form, 'agreement_model_form.onResponse');
    }

    function addDatesPeriodAction(sfWebRequest $request, AgreementModel $model)
    {
        //Дата окончания действия сертификата для концепции
        $modelSett = AgreementModelSettingsTable::getInstance()->createQuery()->where('model_id = ?', $model->getId())->fetchOne();
        if (!$modelSett)
            $modelSett = new AgreementModelSettings();

        $modelSett->setArray(array('model_id' => $model->getId(),
            'certificate_date_to' => date('Y-m-d', strtotime(str_replace('.', '-', $request->getParameter('date_of_certificate_end'))))));
        $modelSett->save();

        //Переиоды проведения мероприятий
        AgreementModelDatesTable::getInstance()->createQuery()->where('model_id = ?', $model->getId())->delete()->execute();

        $datesIndex = 0;
        $datesFieldsStart = $request->getParameter('dates_of_service_action_start');
        $datesFieldsEnd = $request->getParameter('dates_of_service_action_end');

        foreach ($datesFieldsStart as $dateField) {
            $date1 = date('Y-m-d', strtotime(str_replace('.', '-', $dateField)));
            $date2 = date('Y-m-d', strtotime(str_replace('.', '-', $datesFieldsEnd[$datesIndex++])));

            $joinDate = sprintf('%s/%s', $date1, $date2);

            $dateModel = new AgreementModelDates();
            $dateModel->setArray(array('model_id' => $model->getId(),
                'activity_id' => $this->getActivity($request)->getId(),
                'dealer_id' => $this->getUser()->getAuthUser()->getDealer()->getId(),
                'date_of' => $joinDate));
            $dateModel->save();
        }

    }

    function executeEdit(sfWebRequest $request)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url', 'Asset', 'Tag'));

        $model = $this->getModel($request);

        $this->getUser()->setAttribute('editor_link', '');

        if ($model) {
            $reportId = $model->getReportId();
            $result = array(
                'success' => true,
                'values' => array(
                    'id' => $model->getId(),
                    'activity_id' => $model->getActivityId(),
                    'activity' => $model->getActivity()->getName(),
                    'name' => $model->getName(),
                    'blank_id' => $model->getBlankId(),
                    'model_type_id' => $model->getModelTypeId(),
                    'task_id' => $model->getTaskId(),
                    'target' => $model->getTarget(),
                    'cost' => $model->getCost(),
                    'status' => $model->getStatus(),
                    'css_status' => $model->getCssStatus(),
                    'isOutOfDate' => $model->getIsBlocked() && !$model->getAllowUseBlocked(),
                    'model_blocked' => $model->getIsBlocked() && !$model->getAllowUseBlocked(),
                    'allowUseBlocked' => $model->getAllowUseBlocked(),
                    'accept_in_model' => $model->getAcceptInModel(),
                    'haveReport' => empty($reportId) ? 0 : 1,
                    'reportStatus' => !empty($reportId) ? $model->getReport()->getStatus() : '',
                    'no_model_changes' => $model->getNoModelChanges(),
                    'model_accepted_in_online_redactor' => $model->getModelAcceptedInOnlineRedactor(),
                    'model_file' => $model->getModelFile() ? array(
                        'path' => url_for('@agreement_model_download_file?file=' . $model->getModelFile()),
                        'name' => $model->getModelFile(),
                        'size' => $model->getModelFileNameHelper()->getSmartSize()
                    ) : '',
                    'step1' => $model->getStep1() == 'accepted' ? true : false,
                    'step2' => $model->getStep2() == 'accepted' ? true : false,
                    'step1_value' => $model->getStep1(),
                    'step2_value' => $model->getStep2(),
                    'editor_link' => $model->getEditorLink(),
                    'concept_id' => $model->getConceptId(),
                    'uploaded_files_count' => $model->getUploadedFilesCount(),
                    'max_upload_files_count' => sfConfig::get('app_max_files_upload_count'),
                    'isModelScenario' => $model->isModelScenario(),
                    'is_model_scenario' => $model->isModelScenario(),
                    'model_type_data' => $this->makeModelTypeLabel($model),
                )
            );

            if ($model->isModelScenario()) {
                $result['values']['model_uploaded_scenario_files'] = $model->makeListOfUploadedFilesByType(AgreementModel::BY_SCENARIO);
                $result['values']['model_uploaded_record_files'] = $model->makeListOfUploadedFilesByType(AgreementModel::BY_RECORD);
            } else {
                $result['values']['model_uploaded_files'] = $model->makeListOfUploadedFilesByType(AgreementModel::UPLOADED_FILE_MODEL);
            }

            if ($model->getEditorLink()) {
                $result['values']['model_file'] = $model->getModelFile() ? array(
                    'path' => $model->getModelFile(),
                    'name' => $model->getModelFile(),
                    'size' => Utils::getRemoteFileSize($model->getModelFile())
                ) : '';
            } else {
                $result['values']['model_file'] = $model->getModelFile() ? array(
                    'path' => url_for('@agreement_model_download_file?file=' . $model->getModelFile()),
                    'name' => $model->getModelFile(),
                    'size' => $model->getModelFileNameHelper()->getSmartSize()
                ) : '';
            }

            $prefix = $model->getModelType()->getIdentifier();
            foreach ($model->getValuesByType() as $name => $value)
                $result['values'][$prefix . '[' . $name . ']'] = $value;
        } else {
            $result = array(
                'success' => false,
                'error' => 'not_found'
            );
        }

        return $this->sendJson($result);
    }

    function executeDelete(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        if ($model && ($model->getStatus() == 'not_sent' || $model->getStatus() == 'declined')) {
            $text = $model->isConcept() ? 'Концепция удалена.' : 'Макет удалён.';
            $entry = LogEntryTable::getInstance()->addEntry(
                $this->getUser()->getAuthUser(),
                $model->isConcept() ? 'agreement_concept' : 'agreement_model',
                'delete',
                $model->getActivity()->getName() . '/' . $model->getName(),
                $text,
                '',
                $model->getDealer(),
                $model->getId(),
                'agreement'
            );

            $model->createPrivateLogEntryForSpecialists($entry);

            $model->removeUploadedFiles();
            $model->delete();

            $utils = new AgreementActivityStatusUtils($model->getActivity(), $model->getDealer());
            $utils->updateActivityAcceptance();
        }

        return sfView::NONE;
    }

    function executeCancel(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        if ($model && $model->getStatus() != 'accepted') {
            $model->setStatus('not_sent');

            $model->setStep1('none');
            $model->setStep2('none');

            $model->save();

            RealBudgetTable::getInstance()->removeByObjectOnly(ActivityModule::byIdentifier('agreement'), $model->getId());

            $text = $model->isConcept() ? 'Отменена отправка концепции на согласование.' : 'Отменена отправка макета на согласование.';
            $entry = LogEntryTable::getInstance()->addEntry(
                $this->getUser()->getAuthUser(),
                $model->isConcept() ? 'agreement_concept' : 'agreement_model',
                'cancel',
                $model->getActivity()->getName() . '/' . $model->getName(),
                $text,
                '',
                $model->getDealer(),
                $model->getId(),
                'agreement'
            );

            $model->createPrivateLogEntryForSpecialists($entry);
            $model->cancelSpecialistSending();

            $this->addMessageToDiscussion($model, $text);

            AgreementManagementHistoryMailSender::send(
                'AgreementCancelModelMail',
                $entry,
                false,
                false,
                $model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        }

        return $this->sendJson(array('success' => true));
    }

    function executeCancelScenario(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        if ($model && $model->getStatus() != 'accepted') {
            $model->setStatus('not_sent');

            $model->setStep1('none');
            $model->setStep2('none');

            $model->save();

            $text = 'Отменена отправка сценария на согласование.';
            $entry = LogEntryTable::getInstance()->addEntry(
                $this->getUser()->getAuthUser(),
                'agreement_model_scenario',
                'cancel',
                $model->getActivity()->getName() . '/' . $model->getName(),
                $text,
                '',
                $model->getDealer(),
                $model->getId(),
                'agreement'
            );

            $model->createPrivateLogEntryForSpecialists($entry);
            $model->cancelSpecialistSending();

            $this->addMessageToDiscussion($model, $text);

            AgreementManagementHistoryMailSender::send(
                'AgreementCancelModelScenarioMail',
                $entry,
                false,
                false,
                $model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        }

        return $this->sendJson(array('success' => true));
    }

    function executeCancelRecord(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        if ($model && $model->getStatus() != 'accepted') {
            $model->setStatus('not_sent');
            $model->setStep2('none');

            $model->save();

            $text = 'Отменена отправка записи на согласование.';
            $entry = LogEntryTable::getInstance()->addEntry(
                $this->getUser()->getAuthUser(),
                'agreement_model_record',
                'cancel',
                $model->getActivity()->getName() . '/' . $model->getName(),
                $text,
                '',
                $model->getDealer(),
                $model->getId(),
                'agreement'
            );

            $model->createPrivateLogEntryForSpecialists($entry);
            $model->cancelSpecialistSending();

            $this->addMessageToDiscussion($model, $text);
            AgreementManagementHistoryMailSender::send(
                'AgreementCancelModelRecordMail',
                $entry,
                false,
                false,
                $model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        }

        return $this->sendJson(array('success' => true));
    }

    function executeDeleteModelFile(sfWebRequest $request)
    {
        $fileId = $request->getParameter('fileId');
        $file = AgreementModelReportFilesTable::getInstance()->find($fileId);
        if ($file) {
            $file->delete();

            $model = AgreementModelTable::getInstance()
                ->createQuery()
                ->where('id = ?', array($file->getObjectId()))
                ->fetchOne();

            if ($model) {
                $model->reindexFiles();

                unlink($filePath = sfConfig::get('app_uploads_path') . '/' . AgreementModel::MODEL_FILE_PATH . '/' . $file->getFile());

                $this->childs = true;
                $this->model = $model;
                if ($model->isModelScenario() && $model->getStep1() == "accepted") {
                    $this->setTemplate('modelRecordBlock');
                } else {
                    $this->setTemplate('modelFilesBlock');
                }
            }
        } else {
            $this->setTemplate('modelFilesBlock');
        }
    }

    function outputModelTypes()
    {
        $this->model_types = AgreementModelTypeTable::getInstance()
            ->createQuery()
            ->where('concept=?', 0)
            ->orderBy('id DESC')
            ->execute();
    }

    function outputTaskList($request)
    {
        $activity = ActivityTable::getInstance()->find($request->getParameter('activity'));

        if (!empty($activity))
            return $activity->getTasks();
        //$this->task_lists =

        return null;
    }

    function outputModelTypesFields()
    {
        $fields = array();
        $place_fields = array();

        $db_fields = AgreementModelFieldTable::getInstance()
            ->createQuery('f')
            ->innerJoin('f.ModelType t')
            ->execute();

        foreach ($db_fields as $field) {
            if ($field->isPlaceField()) {
                if (!isset($place_fields[$field->getModelTypeId()])) {
                    $place_fields[$field->getModelTypeId()] = array();
                }

                $place_fields[$field->getModelTypeId()][] = AgreementModelFieldRendererFactory::getInstance()->create($field);
            } else {
                if (!isset($fields[$field->getModelTypeId()])) {
                    $fields[$field->getModelTypeId()] = array();
                }

                $fields[$field->getModelTypeId()][] = AgreementModelFieldRendererFactory::getInstance()->create($field);
            }
        }

        $this->model_types_fields = $fields;
        $this->model_place_fields = $place_fields;
    }

    function outputModels(sfWebRequest $request)
    {
        $sorts = array(
            'id' => 'm.id',
            'name' => 'm.name',
            'cost' => 'm.cost'
        );

        $sort_column = $this->getSortColumn();
        $sort_direct = $this->getSortDirection();

        $sql_sort = 'm.id';
        if (isset($sorts[$sort_column]))
            $sql_sort = $sorts[$sort_column] . ' ' . ($sort_direct ? 'DESC' : 'ASC');

        $activity = $this->getActivity($request);

        $models_result = $activity->getModelsList($this->getUser(), $sql_sort, $this->current_q);
        $this->models = isset($models_result['models']) ? $models_result['models'] : array() ;
    }

    function outputBlanks(sfWebRequest $request)
    {
        $dealer = $this->getUser()->getAuthUser()->getDealer();
        if (!$dealer) {
            return array();
        }

        $this->blanks = AgreementModelBlankTable::getInstance()
            ->createQuery('b')
            ->select('b.*, mt.*')
            ->leftJoin('b.Models m WITH m.dealer_id=?', $dealer->getId())
            ->innerJoin('b.ModelType mt')
            ->where('b.activity_id=? and m.id is null', $this->getActivity($request)->getId())
            ->execute();
    }

    function outputDealerFiles()
    {
        $this->dealer_files = $this->getUser()->getAuthUser()->getDealerFiles();
    }

    function outputHasConcept(sfWebRequest $request)
    {
        $this->has_concept = $this->getActivity($request)->getHasConcept();
    }

    function outputConcept()
    {
        $dealer = $this->getUser()->getAuthUser()->getDealer();
        if (!$dealer) {
            return array();
        }

        $this->concept = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->select('m.*')
            ->innerJoin('m.ModelType mt WITH mt.concept=?', true)
            ->where('m.dealer_id=?', $dealer->getId())
            ->andWhere('m.activity_id=?', $this->getActivity($this->getRequest())->getId())
            //->fetchOne();
            ->execute();
    }

    function outputConceptType()
    {
        $this->concept_type = AgreementModelTypeTable::getInstance()
            ->createQuery()
            ->where('concept<>?', 0)
            ->fetchOne();
    }

    function updateModelValuesByType(AgreementModel $model, sfWebRequest $request)
    {
        $model->setValuesByType($this->cleanValues($request->getParameter($model->getModelType()->getIdentifier())));
    }

    protected function cleanValues($values)
    {
        foreach ($values as &$value)
            $value = trim(strip_tags($value));

        return $values;
    }

    function getModelFiles(sfWebRequest $request, $update = false)
    {
        $files = $request->getFiles();
        if (!is_array($files)) {
            return $files;
        }

        /*if (isset($files['model_file']) && isset($files['model_file']['tmp_name']) && $files['model_file']['tmp_name']) {
            return $files;
        }*/

        $uploaded_files = $this->getUploadedFilesByField($files, $this->getModel($request), $update);

        if (!empty($uploaded_files)) {
            return $uploaded_files;
        }

        if ($update) {
            foreach ($files as $key => $data) {
                if ($files[$key] && isset($files[$key]['tmp_name']) && $files[$key]['tmp_name']) {
                    $this->uploaded_files_result[$key] = $files[$key];
                }
            }

            if (count($this->uploaded_files_result) > 0) {
                return $this->uploaded_files_result;
            }
        }

        $server_file = $request->getPostParameter('server_model_file');
        if (!$server_file || preg_match('#[\\\/]#', $server_file)) {
            if (isset($files['model_file']) && isset($files['model_file'][0])) {
                return array('model_file_1' => $files['model_file'][0]);
            }

            return $files;
        }

        $tmp_name = $this->getUser()->getAuthUser()->getDealerUploadPath() . '/' . $server_file;
        if (!file_exists($tmp_name)) {
            return $files;
        }

        $files['model_file'] = array(
            'name' => $server_file,
            'tmp_name' => $tmp_name,
            'type' => F::getFileMimeType($server_file)
        );

        return $files;
    }

    private function getUploadedFilesByField($files, $model = null, $update = false)
    {
        $fields = array('model_file', 'model_record_file');

        $max_upload_files = sfConfig::get('app_max_files_upload_count');
        $ind = $file_ind = 1;
        $count_result = 0;

        if ($model && $model->getId()) {
            $count_result = $model->getUploadedFilesCount();

            if ($model->isModelScenario()) {
                if ($model->getStep1() != "accepted" && isset($count_result[AgreementModel::BY_SCENARIO])) {
                    $file_ind = $count_result[AgreementModel::BY_SCENARIO];
                } else if ($model->getStep1() == "accepted" && isset($count_result[AgreementModel::BY_RECORD])) {
                    $file_ind = $count_result[AgreementModel::BY_RECORD];
                }

                $ind = $file_ind > 0 ? $file_ind : 1;
            }
        }

        $this->uploaded_files_result = array();

        foreach ($files as $key => $file) {
            if (isset($files[$key]['tmp_name']) && $files[$key]['tmp_name']) {
                $this->uploaded_files_result[$key] = $files[$key];
            }
        }

        foreach ($fields as $field) {
            if (isset($files[$field]) && count($files[$field]) > 1) {
                foreach ($files[$field] as $key => $values) {
                    if ($ind > $max_upload_files) {
                        break;
                    }

                    $this->uploaded_files_result[$field . '_' . $ind] = $values;
                    $ind++;
                }
            } else if (isset($files[$field]) && isset($files[$field][0]['tmp_name']) && $files[$field][0]['tmp_name']) {
                $this->uploaded_files_result[$field] = $files[$field][0];
            }
        }

        if (count($this->uploaded_files_result) > 0) {
            foreach ($fields as $field) {
                if (array_key_exists($field, $this->uploaded_files_result)) {
//                    $file_ind = count($this->uploaded_files_result);
                    if ($model && $model->getId()) {
                        if ($count_result && isset($count_result[$field])) {
                            $file_ind = $count_result[$field];
                        }
                    }

                    if ($update) {
                        $file_ind++;
                    }

                    $this->uploaded_files_result[$field . '_' . $file_ind] = $this->uploaded_files_result[$field];
                    unset($this->uploaded_files_result[$field]);
                }
            }
        }

        return $this->uploaded_files_result;
    }

    /**
     * Returns an agreement model
     *
     * @param sfWebRequest $request
     * @return AgreementModel|false
     */
    protected function getModel(sfWebRequest $request)
    {
        $activity = $this->getActivity($request);
        $dealer = $this->getUser()->getAuthUser()->getDealer();

        $model = AgreementModelTable::getInstance()
            ->createQuery()
            ->where('activity_id=? and dealer_id=? and id=?', array($activity->getId(), $dealer->getId(), $request->getParameter('id')))
            ->fetchOne();

        return $model;
    }

    /**
     * @param $model
     * @param $form
     */
    private function saveModelFiles($model, $form, $files_upload_count = 0)
    {

        AgreementModelFileUploadsFactory::getInstance()->createUploadClass(
            $this->getUser()->getAuthUser(),
            $this->uploaded_files_result,
            $model,
            $form,
            $files_upload_count
        );
    }

    protected function attachFileToMessage(AgreementModel $model, Message $message, $editor = false)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());

        if (!$editor) {
            $orig_file = $model->getModelFile();
            if ($model->isModelScenario() && $model->getStep1() == "accepted") {
                $orig_file = $model->getModelRecordFile();
            }

            $file->setFile($message->getId() . '-' . $orig_file);
            copy(
                sfConfig::get('sf_upload_dir') . '/' . AgreementModel::MODEL_FILE_PATH . '/' . $orig_file,
                sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
            );
        } else {
            $file->setFile($model->getModelFile());
            $file->setEditor(true);
        }

        $file->save();
    }

    /**
     * Добавление загруженных файлов к сообщению заявки
     * @param AgreementModel $model
     * @param Message $message
     * @param $form
     * @param array $saved_files
     */
    protected function attachFilesToMessage(AgreementModel $model, Message $message, $form, $saved_files = array())
    {
        if (!empty($saved_files)) {
            foreach ($saved_files as $file_item) {
                $path = $file_item['gen_file_name'];

                if (isset($file_item['upload_path']) && !empty($file_item['upload_path'])) {
                    $path = sprintf('%s/%s', $file_item['upload_path'], $file_item['gen_file_name']);
                }

                $this->saveMessageFile($message, $path);
            }
        } else {
            $query = AgreementModelReportFilesTable::getInstance()->createQuery()->select('file, path')
                ->where('object_id = ?', $model->getId())
                ->orderBy('id ASC');

            if ($model->isModelScenario() && $model->getStep1() != 'accepted') {
                $query->andWhere('object_type = ? and (file_type = ? or file_type = ?)', array(AgreementModel::UPLOADED_FILE_MODEL, AgreementModel::UPLOADED_FILE_MODEL_TYPE, AgreementModel::UPLOADED_FILE_SCENARIO_TYPE));
            }

            $files_list = $query->execute();
            foreach ($files_list as $file_item) {
                $this->saveMessageFile($message, $file_item->getFileName());
            }
        }
    }

    private function saveMessageFile($message, $fileName)
    {
        if ($fileName && file_exists(sfConfig::get('sf_upload_dir') . '/' . AgreementModel::MODEL_FILE_PATH . '/' . $fileName)) {
            $file = new MessageFile();

            $file->setMessageId($message->getId());
            $file->setFile($message->getId() . '-' . basename($fileName));

            copy(
                sfConfig::get('sf_upload_dir') . '/' . AgreementModel::MODEL_FILE_PATH . '/' . $fileName,
                sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
            );

            $file->save();
        }
    }

    /**
     * Add message to discussion
     *
     * @param AgreementModel $model
     * @param string $text
     * @return Message|false
     */
    protected function addMessageToDiscussion(AgreementModel $model, $text)
    {
        $discussion = $model->getDiscussion();

        if (!$discussion)
            return;

        $message = new Message();
        $user = $this->getUser()->getAuthUser();
        $message->setDiscussionId($discussion->getId());
        $message->setUser($user);
        $message->setUserName($user->selectName());
        $message->setText($text);
        $message->setSystem(true);
        $message->save();

        // mark as unread
        $discussion->getUnreadMessages($user);

        return $message;
    }

    function getSortColumn()
    {
        return $this->getUser()->getAttribute(self::SORT_ATTR, 'id');
    }

    function getSortDirection()
    {
        return $this->getUser()->getAttribute(self::SORT_DIRECT_ATTR, false);
    }

    function setSortColumn($column)
    {
        $this->getUser()->setAttribute(self::SORT_ATTR, $column);
    }

    function setSortDirection($direction)
    {
        $this->getUser()->setAttribute(self::SORT_DIRECT_ATTR, $direction);
    }

    public function executeChangeModelPeriod(sfWebRequest $request)
    {
        $modelId = $request->getParameter('modelId');
        $fieldId = $request->getParameter('fieldId');
        $period = $request->getParameter('period');

        $modelValue = AgreementModelValueTable::getInstance()->createQuery()->where('model_id = ? and field_id = ?', array($modelId, $fieldId))->fetchOne();
        if ($modelValue) {
            $modelValue->setValue($period);
            $modelValue->save();
        }

        return sfView::NONE;
    }

    private function setModelChanges($model, $modelTypeId, $noModelChanges)
    {
        if ($noModelChanges && $model->getStep1() != "accepted" && ($modelTypeId == 2 || $modelTypeId == 4)) {
            $model->setStep1('accepted');
            $model->setStep2('accepted');

            $model->setStatus('accepted');

            $model->save();
        } else if ($noModelChanges && $model->getStatus() != "accepted") {
            $model->setStatus('accepted');
            $model->save();
        }
    }

    function executeModelRecordBlock(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        if ($id != 0) {
            $this->childs = $request->getParameter('childs');
            $this->model = AgreementModelTable::getInstance()->find($id);
        }
    }

    function executeModelFilesBlock(sfWebRequest $request)
    {
        $id = $request->getParameter('id');

        if ($id != 0) {
            $this->childs = $request->getParameter('childs');
            $this->model = AgreementModelTable::getInstance()->find($id);
        }
    }

    function executeAddExternal(sfWebRequest $request)
    {
        $activityId = $request->getParameter('activity');

        $this->link = str_replace('-', '/', $request->getParameter('link'));
        $this->link = base64_decode($this->link);

        $this->hash = $request->getParameter('hash');

        if ($this->hash != md5($activityId . $this->link . self::REDACTOR_KEY)) {
            return sfView::ERROR;
        }

        $this->getUser()->setAttribute('editor_link', $this->link);

        $this->executeIndex($request);
        $this->setTemplate('index');
    }

    function executeAddManyConcepts(sfWebRequest $request)
    {

    }

    public function executeModelDatesField(sfWebRequest $request)
    {
    }

    public function executeLoadModelDatesAndCertificates(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $this->model = AgreementModelTable::getInstance()->find($id);
        if ($this->model->getActivity()->getAllowCertificate()) {
            $this->dates = AgreementModelDatesTable::getInstance()->createQuery()->select('date_of')->where('model_id = ?', $id)->orderBy('id ASC')->execute();

            $settModel = AgreementModelSettingsTable::getInstance()->createQuery()->where('model_id = ?', $id)->fetchOne();
            if ($settModel)
                $this->certificateDate = $settModel->getCertificateDateTo();
        } else
            return sfView::NONE;
    }

    public function executeDatesDelete(sfWebRequest $request)
    {
        $id = $request->getParameter('id');

        $date = AgreementModelDatesTable::getInstance()->find($id);
        if ($date) {
            $date->delete();

            return $this->sendJson(array('success' => true));
        }

        return $this->sendJson(array('success' => false));
    }

    public function executeDownloadFile(sfWebRequest $request)
    {
        $file_id = $request->getParameter('file');

        $file_item = AgreementModelReportFilesTable::getInstance()->find($file_id);
        if ($file_item) {
            $path = AgreementModel::MODEL_FILE_PATH;
            if ($file_item->getFileType() == AgreementModelReport::UPLOADED_FILE_ADDITIONAL) {
                $path = AgreementModelReport::ADDITIONAL_FILE_PATH;
            } else if ($file_item->getFileType() == AgreementModelReport::UPLOADED_FILE_FINANCIAL) {
                $path = AgreementModelReport::FINANCIAL_DOCS_FILE_PATH;
            }

            $filePath = sfConfig::get('app_uploads_path') . '/' . $path . '/' . $file_item->getFileName();
            if (file_exists($filePath)) {
                $file = end(explode('/', $filePath));

                if (!F::downloadFile($filePath, $file)) {
                    $this->getResponse()->setContentType('application/json');
                    $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
                }
            }
            else {
                $this->getResponse()->setContentType('application/json');
                $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
            }
        }

        return sfView::NONE;
    }

    public function executeLoadConceptCertFields(sfWebRequest $request)
    {
    }

    /**
     * @param sfWebRequest $request
     */
    public function executeDeleteModelUploadedFile(sfWebRequest $request)
    {
        $file_id = $request->getParameter('id');

        $file_item = AgreementModelReportFilesTable::getInstance()->find($file_id);
        if ($file_item) {
            $description = '';

            if ($file_item->getObjectType() == AgreementModel::UPLOADED_FILE_MODEL) {
                $this->model = AgreementModelTable::getInstance()
                    ->createQuery()
                    ->where('id = ?', $file_item->getObjectId())
                    ->fetchOne();

                $description = sprintf('Файл %s был удален из заявки №%s', $file_item->getFile(), $file_item->getObjectId());
            } else if($file_item->getObjectType() == AgreementModelReport::UPLOADED_FILE_REPORT) {
                $description = sprintf('Файл %s был удален из отчета №%s', $file_item->getFile(), $file_item->getObjectId());
            }

            $log_item = new LogEntry();
            $log_item->setArray(array(
                'user_id' => $this->getUser()->getAuthUser()->getId(),
                'description' => $description,
                'object_id' => $file_item->getObjectId(),
                'action' => 'uploaded_file_delete',
                'object_type' => 'agreement_model_report',
                'login' => $this->getUser()->getAuthUser()->getEmail(),
                'title' => 'Удаление файла',
                'module_id' => 1
            ));
            $log_item->save();
        }

        $this->setTemplate('modelFilesBlock');
        if ($file_item) {
            if ($file_item->getFileType() == AgreementModel::UPLOADED_FILE_RECORD_TYPE) {
                $this->setTemplate('modelRecordBlock');
            }

            $file_item->delete();
        }
    }

    /**
     * @param $object
     * @return array
     */
    private function makeModelTypeLabel($object) {
        $model_type = AgreementModelTypeTable::getInstance()->createQuery()->where('id = ?', $object instanceof AgreementModel ? $object->getModelTypeId() : $object->getParameter('type_id'))->fetchOne();

        if ($model_type) {
            return array(
                'label' => $model_type->getAgreementType() != 'simple' ? explode(';', $model_type->getFieldDescription()) : 'Макет',
                'is_scenario_record' => $model_type->getAgreementType() != 'simple' ? true : false
            );
        }

        return array();
    }

    /**
     * Проверка на наличе даты в календаре
     * @param sfWebRequest $request
     * @return string
     */
    public function executeCheckDateInCalendar(sfWebRequest $request) {
        $dates = CalendarTable::getCalendarDates();

        $result_dates = array();
        foreach ($dates as $date) {
            $elapsed_days = Utils::getElapsedTime(strtotime($date['end_date']) - strtotime($date['start_date']));
            $result_dates[] = date('Y-n-j', strtotime($date['start_date']));

            if ($elapsed_days > 0) {
                for ($inc_day = 1; $inc_day <= $elapsed_days; $inc_day++) {
                    $result_dates[] = date('Y-n-j', strtotime('+'.$inc_day.' days', strtotime($date['start_date'])));
                }
            } else {
                $result_dates[] = date('Y-n-j', strtotime($date['end_date']));
            }

        }

        return $this->sendJson(array('dates' => $result_dates));
    }
}
