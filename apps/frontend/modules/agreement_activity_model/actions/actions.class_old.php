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

  protected $check_for_module = 'agreement';

  /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  function executeIndex(sfWebRequest $request)
  {
    $this->year = $request->getParameter('year');

    $this->outputActivity($request);
    $this->outputHasConcept($request);
    $this->outputConcept();
    $this->outputConceptType();
    $this->outputModels($request);
    $this->outputBlanks($request);
    $this->outputModelTypes();
	$this->outputTaskList($request);
    $this->outputModelTypesFields();
    $this->outputActivities($request);

    $this->outputDealerFiles();

    $this->modelId = $request->getParameter('model');
  }

  function outputActivities(sfWebRequest $request) {
    $user = $this->getUser();
    $show_hidden = $user->isAdmin() || $user->isImporter() || $user->isManager();
    
    $query = ActivityTable::getInstance()
             ->createQuery('a')
             ->select('a.id, a.start_date, a.end_date, a.custom_date, a.name, a.brief, a.importance, v.id is_viewed')
             ->leftJoin('a.UserViews v WITH v.user_id=?', $this->getUser()->getAuthUser()->getId())
             ->orderBy('a.importance DESC, sort DESC, a.id DESC');


    if($request->getParameter('year')) {
      $query->andWhere('a.start_date LIKE ?', $this->year.'%')
            ->andWhere('a.end_date LIKE ?', $this->year.'%');
    }
    else
      $query->where('a.finished=?', false);

    //if(!$show_hidden)
    $query->andWhere('a.hide=?', false);

    ActivityTable::checkActivity($user, $query);

    $this->activities = $query->execute();
  }

  function executeActivities(sfWebRequest $request) {
    $this->outputDealerModels($request);

    $this->outputFilter();

    //$this->executeIndex($request);
    
  }

  function outputDealerModels() {
     $sorts = array(
      'id' => 'm.id',
      'dealer' => 'm.dealer_id', // сортировка по id дилеров (фактически - это группировка)
      'name' => 'm.name',
      'cost' => 'm.cost'
    );

    $sort_column = $this->getSortColumn();
    $sort_direct = $this->getSortDirection();    

    $sql_sort = 'm.id DESC';
    if(isset($sorts[$sort_column]))
      $sql_sort = $sorts[$sort_column].' '.($sort_direct ? 'DESC' : 'ASC');
    
    $query = AgreementModelTable::getInstance()
             ->createQuery('m')
             ->innerJoin('m.Activity a')
             ->innerJoin('m.ModelType mt WITH mt.concept=?', false)
             ->leftJoin('m.Discussion d')
             ->leftJoin('m.Report r')
             ->orderBy($sql_sort);
    
    $query->andWhere('m.dealer_id=?', $this->getUser()->getAuthUser()->getDealer()->getId());

    if($this->getActivityStatusFilter()) {
      switch($this->getActivityStatusFilter()) {
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
    }
    else {
      /*$query->andWhere('m.status = ? or m.status = ? or m.status = ?', array('declined', 'not_sent', 'accepted'))
            ->andWhere('m.report_id is null');/*/

        $query->andWhere('m.status != ?', 'accepted');
    }

    if($this->getStartDateFilter())
      $query->andWhere('m.created_at>=?', D::toDb($this->getStartDateFilter()));
    if($this->getEndDateFilter())
      $query->andWhere('m.created_at<=?', D::toDb($this->getEndDateFilter()));

    $this->models = $query->execute();

    $isDealer = $this->getUser()->isDealerUser();
    if($this->getUser()->isImporter())
      $isDealer = false;
    
    $mods = array();
    foreach($this->models as $m) {
      $mods[strtotime($this->getActivityStatusFilter() != 'process_reports' ? $m->getModelAcceptToDate($isDealer) : $m->getDateModelAccept())] = $m;
    }

    ksort($mods, SORT_NUMERIC);

    $result = array();
    foreach($mods as $key => $model) {
      $status = $model->getReportCssStatus();

      $date = $key;
      if($status == 'ok')
        $date = $model->getReport()->getAcceptDate();

      $year = D::getYear($date);

      $prevYear = D::isPrevYear($date);
      if($status == 'ok' && $prevYear)
        $year--;
            
      $result[$year]['data'][] = array('date' => $key, 'model' => $model, 'status' => $status == 'ok' ? $prevYear : false);
      if($status == 'ok')
        $result[$year]['summ'] += $model->getCost();

    }

    /*$results = array();
    $it = end($result);
    do {
      array_push($results, $it);
    } while ($it = prev($result));*/

    $this->models = $result;
  }

  function outputActivitystatusFilter() {
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
  

  function getActivityStatusFilter() {
    $default = $this->getUser()->getAttribute('activity_status', '', self::FILTER_NAMESPACE);
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
    
    if($column == $cur_column)
    {
      $direction = !$direction;
    }
    else
    {
      $direction = false;
      $cur_column = $column;
    }
    
    $this->setSortColumn($cur_column);
    $this->setSortDirection($direction);
    
    $this->redirect('@agreement_module_models?activity='.$this->getActivity($request)->getId());
  }
  
  function executeAdd(sfWebRequest $request)
  {
    $draft = $request->getParameter('draft', 'false') == 'true';
    $blank_id = $request->getParameter('blank_id');
    $model_type_id = $request->getParameter('model_type_id');
	$task_id = $request->getParameter('task_id');
	
    if($blank_id)
    {
      $blank = AgreementModelBlankTable::getInstance()->find($blank_id);
      // существование болванки проверит валидатор формы
      if($blank)
        $model_type_id = $blank->getModelTypeId();
    }

    $no_model_changes = $request->getParameter('no_model_changes');
    $no_model_changes = is_null($no_model_changes) ? false : true;

    $model_accepted_in_online_redactor = $request->getParameter('model_accepted_in_online_redactor');
    $model_accepted_in_online_redactor = is_null($model_accepted_in_online_redactor) ? false : true;

    $form = new AgreementModelForm($draft);
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
        'model_accepted_in_online_redactor' => $model_accepted_in_online_redactor
      ), 
      $this->getModelFiles($request)
    );
	
	if($form->isValid())
    {
      $form->save();
      
      $model = $form->getObject();
      $this->updateModelValuesByType($model, $request);

      if(!$no_model_changes && ($model_type_id == 2 || $model_type_id == 4)) {
        $model->setModelRecordFile('-');
        $model->save();
      }

      if(!$draft)
      {
        $utils = new AgreementActivityStatusUtils($model->getActivity(), $model->getDealer());
        $utils->updateActivityAcceptance();
        
        $text = $model->isConcept() ? 'Концепция отправлена на согласование' : 'Макет отправлен на согласование';
        $entry = LogEntryTable::getInstance()->addEntry(
          $this->getUser()->getAuthUser(), 
          $model->isConcept() ? 'agreement_concept' : 'agreement_model', 
          'add', 
          $model->getActivity()->getName().'/'.$model->getName(),
          $text, 
          'clip',
          $model->getDealer(),
          $model->getId(),
          'agreement'
        );

        $message = $this->addMessageToDiscussion($model, $text);

        $this->attachFileToMessage($model, $message);
        $this->attachFilesToMessage($model, $message);

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
  
  function executeUpdate(sfWebRequest $request)
  {
    $model = $this->getModel($request);
    if(!$model)
      return $this->sendJson(array('success' => false, 'error' => 'not_found'), 'agreement_model_form.onResponse');
    
    if($model->getStep1() != 'accepted') {
      if($model->getStatus() != 'not_sent' && $model->getStatus() != 'declined')
        return $this->sendJson(array('success' => false, 'error' => 'wrong_status'), 'agreement_model_form.onResponse');
    }
    
    $draft = $request->getParameter('draft', 'false') == 'true';

    $no_model_changes = $request->getParameter('no_model_changes');
    $no_model_changes = is_null($no_model_changes) ? false : true;

    $model_accepted_in_online_redactor = $request->getParameter('model_accepted_in_online_redactor'); 
    $model_accepted_in_online_redactor = is_null($model_accepted_in_online_redactor) ? false : true;

    $model_type_id = $model->getModelType()->getId();
  	$blank_id = $request->getParameter('blank_id');
  	if(!$blank_id) {
      	$model_type_id = $request->getParameter('model_type_id');
  	}

    $form = new AgreementModelForm($draft, $model);
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
        'model_accepted_in_online_redactor' => $model_accepted_in_online_redactor
      ), 
      $this->getModelFiles($request)
    );
    
    if($form->isValid())
    {
      $form->save();
      
      $model = $form->getObject();
      
      $modelType = AgreementModelTypeTable::getInstance()->find($model_type_id);
      $model->setModelType($modelType);
      $model->save();

      $this->updateModelValuesByType($model, $request);

      $model->setStep2('none');
      $model->save();

      if(!$draft)
      {
        $utils = new AgreementActivityStatusUtils($model->getActivity(), $model->getDealer());
        $utils->updateActivityAcceptance();
        
        $text = $model->isConcept() ? 'Концепция отправлена на согласование' : 'Макет отправлен на согласование';
        $entry = LogEntryTable::getInstance()->addEntry(
          $this->getUser()->getAuthUser(), 
          $model->isConcept() ? 'agreement_concept' : 'agreement_model', 
          'edit', 
          $model->getActivity()->getName().'/'.$model->getName(),
          $text, 
          'clip',
          $model->getDealer(),
          $model->getId(),
          'agreement'
        );

        $model->createPrivateLogEntryForSpecialists($entry);

        $message = $this->addMessageToDiscussion($model, $text);
        if($form->getValue('model_file'))
          $this->attachFileToMessage($model, $message);

        $this->attachFilesToMessage($model, $message, $form);

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
  
  function executeEdit(sfWebRequest $request)
  {
    $result = array();
    $model = $this->getModel($request);
    
    if($model)
    {
      $modelRecordFile = $model->getModelRecordFile();
      if($modelRecordFile != '-') {
        $modelRecordFile = $model->getModelRecordFile() ? array(
            'path' => '/uploads/'.AgreementModel::MODEL_FILE_PATH.'/'.$model->getModelRecordFile(),
            'name' => $model->getModelRecordFile(),
            'size' => $model->getModelRecordFileNameHelper()->getSmartSize()
           ) : '';
      }
      else 
        $modelRecordFile = '';

      $extModelFiles = array();
      for($i = 1; $i <= self::MAX_FILES; $i++) {
        $func = "getModelFile".$i;
        $file = $model->$func();

        $extModelFiles['model_file'.$i] = $file ? array(
            'path' => '/uploads/'.AgreementModel::MODEL_FILE_PATH.'/'.$file,
            'name' => $file,
            'size' => $model->getModelFileNameHelperByFileName($file)->getSmartSize()
           ) : '';
      }

      $extModelRecordFiles = array();
      for($i = 1; $i <= self::MAX_FILES; $i++) {
        $func = "getModelRecordFile".$i;
        $file = $model->$func();

        $extModelRecordFiles['model_record_file'.$i] = $file ? array(
            'path' => '/uploads/'.AgreementModel::MODEL_FILE_PATH.'/'.$file,
            'name' => $file,
            'size' => $model->getModelFileNameHelperByFileName($file)->getSmartSize()
           ) : '';
      }

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
          'isOutOfDate' => $model->isOutOfDate(),
          'accept_in_model' => $model->getAcceptInModel(),
          'haveReport' => empty($reportId) ? 0 : 1,
          'reportStatus' => !empty($reportId) ? $model->getReport()->getStatus() : '',
          'no_model_changes' => $model->getNoModelChanges(),
          'model_accepted_in_online_redactor' => $model->getModelAcceptedInOnlineRedactor(),
          'step1' => $model->getStep1() == 'accepted' ? true : false,
          'step2' => $model->getStep2() == 'accepted' ? true : false,
          'model_file' => $model->getModelFile() ? array(
            'path' => '/uploads/'.AgreementModel::MODEL_FILE_PATH.'/'.$model->getModelFile(),
            'name' => $model->getModelFile(),
            'size' => $model->getModelFileNameHelper()->getSmartSize()
          ) : '',
          'model_record_file' => $modelRecordFile,
          'ext_model_files' => $extModelFiles,
          'ext_model_record_files' => $extModelRecordFiles
        )
      );
      $prefix = $model->getModelType()->getIdentifier();
      foreach($model->getValuesByType() as $name => $value)
        $result['values'][$prefix.'['.$name.']'] = $value;
    }
    else
    {
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
    if($model && ($model->getStatus() == 'not_sent' || $model->getStatus() == 'declined'))
    {
      $text = $model->isConcept() ? 'Концепция удалена' : 'Макет удалён';
      $entry = LogEntryTable::getInstance()->addEntry(
        $this->getUser()->getAuthUser(), 
        $model->isConcept() ? 'agreement_concept' : 'agreement_model', 
        'delete', 
        $model->getActivity()->getName().'/'.$model->getName(),
        $text, 
        '',
        $model->getDealer(),
        $model->getId(),
        'agreement'
      );
      
      $model->createPrivateLogEntryForSpecialists($entry);

      $model->delete();      
      
      $utils = new AgreementActivityStatusUtils($model->getActivity(), $model->getDealer());
      $utils->updateActivityAcceptance();
    }

    return sfView::NONE;
  }
  
  function executeCancel(sfWebRequest $request)
  {
    $model = $this->getModel($request);
    if($model && $model->getStatus() != 'accepted')
    {
      $model->setStatus('not_sent');
      $model->setStep1('none');
      $model->setStep2('none');

      $model->save();
      
      RealBudgetTable::getInstance()->removeByObjectOnly(ActivityModule::byIdentifier('agreement'), $model->getId());

      $text = $model->isConcept() ? 'Отменена отправка концепции на согласование' : 'Отменена отправка макета на согласование';
      $entry = LogEntryTable::getInstance()->addEntry(
        $this->getUser()->getAuthUser(), 
        $model->isConcept() ? 'agreement_concept' : 'agreement_model', 
        'cancel', 
        $model->getActivity()->getName().'/'.$model->getName(),
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

  function executeDeleteModelFile(sfWebRequest $request)
  {
    $modelId = $request->getParameter('modelId');
    $fileId = $request->getParameter('fileId');
    $fileType = $request->getParameter('fileType');
    $isModel = $request->getParameter('isModel');

    if(!$isModel)
    {
      $report = AgreementModelReportTable::getInstance()->createQuery()->where('model_id = ?', $modelId)->fetchOne();

      if($report) {
        if($fileType == 'ext-financial-docs-file' || $fileType == 'concept-report-ext-model-file') 
          $func = "setFinancialDocsFile".$fileId;
        else if($fileType == 'ext-additional-file')
          $func = "setAdditionalFileExt".$fileId;

        $report->$func('');
        $report->save();

        return $this->sendJson(array('success' => true));
      }
    }
    else {
      $model = AgreementModelTable::getInstance()->find($modelId);

      if($model) {

        if($fileType == 'ext-model-record-file')
          $func = 'setModelRecordFile'.$fileId;
        else if($fileType == 'ext-model-file' || $fileType == 'concept-ext-model-file')
          $func = 'setModelFile'.$fileId;          

        $model->$func('');
        $model->save();

        return $this->sendJson(array('success' => true));
      }
    }


    return $this->sendJson(array('success' => false));
  }
  
  function outputModelTypes()
  {
    $this->model_types = AgreementModelTypeTable::getInstance()
                         ->createQuery()
                         ->where('concept=?', 0)
                         ->execute();
  }
  
  function outputTaskList($request) {
	$activity = ActivityTable::getInstance()->find($request->getParameter('activity'));
	
	if(!empty($activity))
		return $activity->getTasks();
	//$this->task_lists = 
	
	return null;
  }
  
  function outputModelTypesFields()
  {
    $fields = array();
    $db_fields = AgreementModelFieldTable::getInstance()
                 ->createQuery('f')
                 ->innerJoin('f.ModelType t')
                 ->execute();
    
    foreach($db_fields as $field)
    {
      if(!isset($fields[$field->getModelTypeId()]))
        $fields[$field->getModelTypeId()] = array();
      
      $fields[$field->getModelTypeId()][] = AgreementModelFieldRendererFactory::getInstance()->create($field);
    }
    
    $this->model_types_fields = $fields;
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
    if(isset($sorts[$sort_column]))
      $sql_sort = $sorts[$sort_column].' '.($sort_direct ? 'DESC' : 'ASC');
    
    $this->models = AgreementModelTable::getInstance()
                    ->createQuery('m')
                    ->innerJoin('m.ModelType mt WITH mt.concept=?', 0)
                    ->leftJoin('m.Report r')
                    ->leftJoin('m.Discussion d')
                    ->where(
                      'm.activity_id=? and m.dealer_id=?', array(
                        $this->getActivity($request)->getId(),
                        $this->getUser()->getAuthUser()->getDealer()->getId()
                      )
                    )
                    ->orderBy($sql_sort)
                    ->execute();
  }
  
  function outputBlanks(sfWebRequest $request)
  {
    $this->blanks = AgreementModelBlankTable::getInstance()
                    ->createQuery('b')
                    ->select('b.*, mt.*')
                    ->leftJoin('b.Models m WITH m.dealer_id=?', $this->getUser()->getAuthUser()->getDealer()->getId())
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
    $this->concept = AgreementModelTable::getInstance()
                     ->createQuery('m')
                     ->select('m.*')
                     ->innerJoin('m.ModelType mt WITH mt.concept=?', true)
                     ->where('m.dealer_id=?', $this->getUser()->getAuthUser()->getDealer()->getId())
                     ->andWhere('m.activity_id=?', $this->getActivity($this->getRequest())->getId())
                     ->fetchOne();
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
    foreach($values as &$value)
      $value = trim(strip_tags($value));
    
    return $values;
  }
  
  function getModelFiles(sfWebRequest $request)
  {
    $files = $request->getFiles();
    if(!is_array($files))
      return $files;
    
    if(isset($files['model_file']) && isset($files['model_file']['tmp_name']) && $files['model_file']['tmp_name'])
      return $files;
    
    $server_file = $request->getPostParameter('server_model_file');
    if(!$server_file || preg_match('#[\\\/]#', $server_file))
      return $files;
    
    $tmp_name = $this->getUser()->getAuthUser()->getDealerUploadPath().'/'.$server_file;
    if(!file_exists($tmp_name))
      return $files;
    
    $files['model_file'] = array(
      'name' => $server_file,
      'tmp_name' => $tmp_name,
      'type' => F::getFileMimeType($server_file)
    );
    
    return $files;
  }

  function getModelRecordFiles(sfWebRequest $request)
  {
    $files = $request->getFiles();
    if(!is_array($files))
      return $files;
    
    if(isset($files['model_record_file']) && isset($files['model_record_file']['tmp_name']) && $files['model_record_file']['tmp_name'])
      return $files;
    
    $server_file = $request->getPostParameter('server_model_record_file');
    if(!$server_file || preg_match('#[\\\/]#', $server_file))
      return $files;
    
    $tmp_name = $this->getUser()->getAuthUser()->getDealerUploadPath().'/'.$server_file;
    if(!file_exists($tmp_name))
      return $files;
    
    $files['model_record_file'] = array(
      'name' => $server_file,
      'tmp_name' => $tmp_name,
      'type' => F::getFileMimeType($server_file)
    );
    
    return $files;
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
    return AgreementModelTable::getInstance()
           ->createQuery()
           ->where('activity_id=? and dealer_id=? and id=?', array($activity->getId(), $dealer->getId(), $request->getParameter('id')))
           ->fetchOne();    
  }
  
  protected function attachFileToMessage(AgreementModel $model, Message $message)
  {
    $file = new MessageFile();
    $file->setMessageId($message->getId());
    $file->setFile($message->getId().'-'.$model->getModelFile());
    
    copy(
      sfConfig::get('sf_upload_dir').'/'.  AgreementModel::MODEL_FILE_PATH.'/'.$model->getModelFile(),
      sfConfig::get('sf_upload_dir').'/'.MessageFile::FILE_PATH.'/'.$file->getFile()
    );
    $file->save();

  }

  protected function attachFilesToMessage(AgreementModel $model, Message $message, $form)
  {
    if($form) {
      if($form->getValue('model_record_file'))
        $this->saveMessageFile($model, $message, 'getModelRecordFile');

      for($i = 1; $i <= self::MAX_FILES; $i++) {
        if($form->getValue('model_file'.$i))
          $this->saveMessageFile($model, $message, 'getModelFile'.$i);

        if($form->getValue('model_record_file'.$i))
          $this->saveMessageFile($model, $message, 'getModelRecordFile'.$i);
      }  
    }
    else {
      $this->saveMessageFile($model, $message, 'getModelRecordFile');

      for($i = 1; $i <= self::MAX_FILES; $i++) {
        $this->saveMessageFile($model, $message, 'getModelFile'.$i);
        $this->saveMessageFile($model, $message, 'getModelRecordFile'.$i);
      }
    }
  }

  private function saveMessageFile($model, $message, $func)
  {
    $file = $model->$func();
    if($file && $file != 'Array') {
      $file = new MessageFile();

      $file->setMessageId($message->getId());
      $file->setFile($message->getId().'-'.$model->$func());

      copy(
        sfConfig::get('sf_upload_dir').'/'.  AgreementModel::MODEL_FILE_PATH.'/'.$model->$func(),
        sfConfig::get('sf_upload_dir').'/'.MessageFile::FILE_PATH.'/'.$file->getFile()
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
      
      if(!$discussion)
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

  public function executeChangeModelPeriod(sfWebRequest $request) {
    $modelId = $request->getParameter('modelId');
    $fieldId = $request->getParameter('fieldId');
    $period = $request->getParameter('period');

    $modelValue = AgreementModelValueTable::getInstance()->createQuery()->where('model_id = ? and field_id = ?', array($modelId, $fieldId))->fetchOne();
    if($modelValue)
    {
      $modelValue->setValue($period);
      $modelValue->save();
    }

    return sfView::NONE;
  }

  private function setModelChanges($model, $modelTypeId, $noModelChanges)
  {
  	if($noModelChanges && $model->getStep1() != "accepted" && ($modelTypeId == 2 || $modelTypeId == 4))
    {
    	$model->setStep1('accepted');
    	$model->setStep2('accepted');	

      	$model->setStatus('accepted');

    	$model->save();
    }
    else if($noModelChanges && $model->getStatus() != "accepted") {
      $model->setStatus('accepted');
      $model->save();
    }
  }

  function executeModelRecordBlock(sfWebRequest $request)
  {
  	$id = $request->getParameter('id');
  	if($id != 0) {
  		$this->childs = $request->getParameter('childs');
  		$this->model = AgreementModelTable::getInstance()->find($id);
  	}
  	
  }

  function executeModelFilesBlock(sfWebRequest $request)
  {
  	$id = $request->getParameter('id');
  	if($id != 0) {
  		$this->childs = $request->getParameter('childs');
  		$this->model = AgreementModelTable::getInstance()->find($id);
  	}
  }
}
