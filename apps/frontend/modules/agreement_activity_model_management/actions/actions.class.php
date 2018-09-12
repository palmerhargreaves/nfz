<?php

include(sfConfig::get('sf_root_dir') . '/lib/dompdf/dompdf_config.inc.php');

ini_set('memory_limit', '1000M');

/**
 * agreement_activity_management actions.
 *
 * @package    Servicepool2.0
 * @subpackage agreement_activity_management
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agreement_activity_model_managementActions extends ActionsWithJsonForm
{
    const SORT_ATTR = 'man_sort';
    const SORT_DIRECT_ATTR = 'man_sort_direct';
    const FILTER_NAMESPACE = 'agreement_filter';
    const FILTER_NAMESPACE_FAVORITES = 'agreement_filter_favorites';

    const DECLINE_MODEL_ACTION = 'decline_model';
    const DECLINE_REPORT_ACTION = 'decline_report';

    const LIMIT_MODELS_COUNT = 50;

    const IMAGE_WIDTH = 1024;

    protected $_dealer_filter = null;
    protected $_activity_filter = null;
    protected $_designer_filter = null;

    protected $_favorites_dealer_filter = null;
    protected $_favorites_activity_filter = null;
    protected $_favorites_activity_finished_filter = null;
    protected $_favorites_model_type_filter = null;

    private $isReset = false;

    function executeIndex(sfWebRequest $request)
    {

        $this->resetModelFilterByOffset();

        $this->getYearFilter($request);
        if ($this->getDesignerFilter())
            $this->outputDesignerModels($request);
        else
            $this->outputModels($request);

        $this->outputConcepts($request);
        $this->outputDeclineReasons();
        $this->outputDeclineReportReasons();
        $this->outputSpecialistGroups();
        $this->outputFilter();
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

        $this->redirect('@agreement_module_management_models');
    }

    function executeModel(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        if (!$model)
            return sfView::ERROR;

        $this->outputSpecialistGroups();

        $this->model = $model;
    }

    function executeReport(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        if (!$model)
            return sfView::ERROR;

        $this->report = $model->getReport();
        $this->model = $model;

        $this->outputSpecialistGroups();
    }

    function executeDeclineModel(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $form = new AgreementDeclineForm(array(), array(
            'comments_file_path' => AgreementModel::AGREEMENT_COMMENTS_FILE_PATH,
            ///'reason_model' => 'AgreementDeclineReason'
        ));

        $form->bind(
            array(
                //'decline_reason_id' => $request->getPostParameter('decline_reason_id'),
                'agreement_comments' => $request->getPostParameter('agreement_comments'),
                'designer_approve' => $request->getPostParameter('designer_approve')
            ),
            array()//$this->getModelFiles($request)
        );

        $model->setManagerStatus('declined');
        //$model->setDesignerStatus('wait');
        $model->save();

        $send_to_specialist = $form->getValue('designer_approve');
        if ($form->isValid()) {
            //Отправляем заявку дизайнеру
            if ($send_to_specialist) {
                return $this->executeSendModelToSpecialists($request, $form, 'decline');
            }

            $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

            $model->workWithScenatioAndRecordsData($request);

            $model->setManagerStatus('wait');
            $model->setDesignerStatus('wait');

            $model->setStatus('declined');
            $model->setDeclineReasonId($form->getValue('decline_reason_id'));

            $model->save();

            $utils = new AgreementModelStatusUtils();
            $utils->declineModel(
                $model,
                $this->getUser()->getAuthUser(),
                null, //AgreementDeclineReasonTable::getInstance()->find($model->getDeclineReasonId()),
                $form->getValue('agreement_comments'),
                $msg_files
            );
        }

        return $this->sendFormBindResult($form, 'window.decline_model_form.onResponse');
    }

    function executeAcceptModel(sfWebRequest $request)
    {
        $action_type = $request->getParameter('action_type');

        if ($action_type == self::DECLINE_MODEL_ACTION) {
            return $this->executeDeclineModel($request);
        }

        //executeSendModelToSpecialists(sfWebRequest $request)

        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $form = new AgreementAcceptForm(array(),
            array(
                'comments_file_path' => AgreementModel::AGREEMENT_COMMENTS_FILE_PATH,
            )
        );

        $form->bind(
            array(
                'agreement_comments' => $request->getPostParameter('agreement_comments'),
                'designer_approve' => $request->getPostParameter('designer_approve')

            ),
            array()//$this->getModelFiles($request)
        );

        $send_to_specialist = $form->getValue('designer_approve');
        if ($form->isValid()) {
            if ($send_to_specialist) {
                return $this->executeSendModelToSpecialists($request, $form, 'accept');
            }

            /**
             * Make copy of uploaded temp files and remove
             */
            $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

            if ($model->isModelScenario()) {
                if ($model->getStep1() != "accepted") {

                    $model->changeStep1Statuses();
                    $model->save();

                    $utils = new AgreementModelStatusUtils();
                    $utils->acceptModel(
                        $model,
                        $this->getUser()->getAuthUser(),
                        $form->getValue('agreement_comments'),
                        $msg_files,
                        fals
                    );

                    return $this->sendFormBindResult($form, 'window.accept_decline_form.onResponse');
                } else if ($model->getStep2() != "accepted") {
                    $model->setStep2("accepted");

                    $model->acceptModelWithMD();
                }
            } else {
                $model->setStatus('accepted');
            }
            $model->save();

            $utils = new AgreementModelStatusUtils();
            $utils->acceptModel(
                $model,
                $this->getUser()->getAuthUser(),
                $form->getValue('agreement_comments'),
                $msg_files,
                false
            );
        }

        return $this->sendFormBindResult($form, 'window.accept_decline_form.onResponse');
    }

    function executeDeclineReportN(sfWebRequest $request)
    {

    }

    function executeDeclineReport(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $form = new AgreementDeclineForm(array(), array(
            'comments_file_path' => AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH,
            //'reason_model' => 'AgreementDeclineReportReason'
        ));
        $form->bind(
            array(
                //'decline_reason_id' => $request->getPostParameter('decline_reason_id'),
                'agreement_comments' => $request->getPostParameter('agreement_comments'),
                'designer_approve' => $request->getPostParameter('designer_approve')
            ),
            array()//$request->getFiles()
        );

        $model->setManagerStatus('wait');
        $model->setDesignerStatus('wait');
        $model->save();

        $send_to_specialist = $form->getValue('designer_approve');
        if ($send_to_specialist) {
            return $this->executeSendReportToSpecialists($request, $form, 'decline');
        }

        if ($form->isValid()) {
            $report = $model->getReport();
            if (!$report) {
                $report = new AgreementModelReport();
            }

            /**
             * Make copy of uploaded temp files and remove
             */
            $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

            $report->setModelId($model->getId());
            $report->setStatus('declined');
            $report->setDeclineReasonId($form->getValue('decline_reason_id'));
            $report->setAgreementComments($form->getValue('agreement_comments'));

            $report->save();

            $model->setReportId($report->getId());
            $model->save();

            $utils = new AgreementReportStatusUtils();
            $utils->declineReport(
                $report,
                $this->getUser()->getAuthUser(),
                null,//AgreementDeclineReasonTable::getInstance()->find($report->getDeclineReasonId()),
                $form->getValue('agreement_comments'),
                $msg_files
            );
        }

        return $this->sendFormBindResult($form, 'window.decline_report_form.onResponse');
    }

    function executeAcceptReport(sfWebRequest $request)
    {
        $action_type = $request->getParameter('action_type');

        if ($action_type == self::DECLINE_REPORT_ACTION) {
            return $this->executeDeclineReport($request);
        }

        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $form = new AgreementAcceptForm(array(),
            array(
                'comments_file_path' => AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH,
            ));

        $form->bind(
            array(
                'agreement_comments' => $request->getPostParameter('agreement_comments'),
                'designer_approve' => $request->getPostParameter('designer_approve')
            ),
            array()//$request->getFiles()
        );

        $send_to_specialist = $form->getValue('designer_approve');
        if ($send_to_specialist) {
            return $this->executeSendReportToSpecialists($request, $form, 'accept');
        }

        if ($form->isValid()) {
            $report = $model->getReport();
            if (!$report) {
                $report = new AgreementModelReport();
            }

            /**
             * Make copy of uploaded temp files and remove
             */
            $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

            $report->setModelId($model->getId());
            $report->setStatus('accepted');
            $report->save();

            $model->setReportId($report->getId());
            $model->save();

            $utils = new AgreementReportStatusUtils();
            $utils->acceptReport($report, $this->getUser()->getAuthUser(), $form->getValue('agreement_comments'), $msg_files);
        }

        return $this->sendFormBindResult($form, 'window.accept_report_form.onResponse');
    }

    function executeSendModelToSpecialists(sfWebRequest $request, $form = null, $status = 'accept')
    {
        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $model->cancelSpecialistSending();

        $agreement_comments = $request->getParameter('agreement_comments');
        $data = $this->getSpecialistData($request);

        if (!$data) {
            return $this->sendFormBindResult($form, 'window.accept_decline_form.onResponse');
        }

        if ($status == 'accept') {
            $model->setManagerStatus('accepted');
            $model->setDesignerStatus('wait');
        } else if ($status == 'decline') {
            $model->setManagerStatus('declined');
        }

        $saved_comment_file = null;
        if (count($data['group']) > 0) {
            /**
             * Make copy of uploaded temp files and remove
             */
            $msg_files = TempFileTable::copyFilesByRequest($request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

            $model->setAgreementCommentsFile('');
            if (!empty($msg_files)) {
                $model->setAgreementCommentsFile($msg_files[0]);
            }

            $comment_text = $form->getValue('agreement_comments');
            $model->setAgreementComments('');
            if (!empty($comment_text) && $status != 'accept') {
                $model->setAgreementComments($comment_text);
            }

            $model->setStatus('wait_specialist');
            $model->save();

            $discussionMsg = $model->isConcept() ? 'Концепция на проверке у дизайнера.' : 'Макет на проверке у дизайнера.';
            if ($model->isModelScenario()) {
                if ($model->getStep1() == "wait" && ($model->getStep2() == "none" || $model->getStep2() == "wait")) {
                    $discussionMsg = 'Сценарий на проверке у дизайнера.';
                } else if ($model->getStep1() == "accepted" && $model->getStep2() != "accepted") {
                    $discussionMsg = 'Запись на проверке у дизайнера.';
                }
            }

            $message = $this->addMessageToDiscussion($model, $discussionMsg);
            //$this->addFileToSpecialistMessage($message, $model->getAgreementCommentsFile(), AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

            LogEntryTable::getInstance()->addEntry(
                $this->getUser()->getAuthUser(),
                $model->isConcept() ? 'agreement_concept' : 'agreement_model',
                'sent_to_specialist',
                $model->getActivity()->getName() . '/' . $model->getName(),
                $discussionMsg,
                '',
                $model->getDealer(),
                $model->getId(),
                'agreement'
            );
        }

        foreach ($data['group'] as $group_id => $true) {
            $specialist = $this->getSpecialist($data, $group_id);
            //$msg = $this->getMessageForSpecialist($data, $group_id);
            $this->sendModelToSpecialist($model, $specialist, $agreement_comments, false);
        }

        if ($status != 'accept') {
            $utils = new AgreementModelStatusUtils();
            $utils->declineModelOnlyMail(
                $model,
                $this->getUser()->getAuthUser(),
                $form->getValue('agreement_comments'),
                $msg_files,
                false
            );
        } else {
            $utils = new AgreementModelStatusUtils();
            $utils->acceptModelOnlyMail(
                $model,
                $this->getUser()->getAuthUser(),
                $form->getValue('agreement_comments'),
                $msg_files,
                $message
            );
        }

        //return $this->sendJson(array('success' => true));
        return $this->sendFormBindResult($form, 'window.accept_decline_form.onResponse');
    }

    private function addFileToSpecialistMessage(Message $message, $file_name, $path)
    {
        if (isset($file_name) && !empty($file_name)) {
            $file = new MessageFile();
            $file->setMessageId($message->getId());
            $file->setFile($message->getId() . '-' . $file_name);

            copy(
                sfConfig::get('sf_upload_dir') . '/' . $path . '/' . $file_name,
                sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
            );

            $file->save();
        }
    }

    protected function sendModelToSpecialist(AgreementModel $model, User $specialist, $msg, $msg_show = true)
    {
        $comment = new AgreementModelComment();
        $comment->setArray(array(
            'model_id' => $model->getId(),
            'user_id' => $specialist->getId()
        ));

        $comment->setStatus('wait');
        $comment->save();

        //$this->sendMessageToSpecialist($model->getDiscussion(), $specialist, $msg ?: 'Отправлено для согласования', $msg_show);

        $log_entry = LogEntryTable::getInstance()->addEntry(
            $this->getUser()->getAuthUser(),
            $model->isConcept() ? 'agreement_concept' : 'agreement_model',
            'sent_to_specialist',
            $model->getActivity()->getName() . '/' . $model->getName(),
            $model->isConcept() ? 'Вам отправлена концепция для согласования' : 'Вам отправлен макет для согласования',
            '',
            $model->getDealer(),
            $model->getId(),
            'agreement'
        );
        $log_entry->setPrivateUser($specialist);
        $log_entry->save();

        AgreementSpecialistHistoryMailSender::send('AgreementModelSentToSpecialistMail', $log_entry, $specialist, $msg);
    }

    function executeSendReportToSpecialists(sfWebRequest $request, $form = null, $status = 'accept')
    {
        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $report = $model->getReport();
        $this->forward404Unless($report);

        $report->cancelSpecialistSending();

        $agreement_comments = $request->getParameter('agreement_comments');
        $data = $this->getSpecialistData($request);

        if (!$data) {
            return $this->sendFormBindResult($form, 'window.accept_model_form.onResponse');
        }

        if ($status == 'accept') {
            $model->setManagerStatus('accepted');
        } else if ($status == 'decline') {
            $model->setManagerStatus('declined');
        }

        $model->setDesignerStatus('wait');
        $model->save();

        if (count($data['group']) > 0) {
            $report->setAgreementComments('');
            if ($agreement_comments) {
                $report->setAgreementComments($agreement_comments);
            }

            /**
             * Make copy of uploaded temp files and remove
             */
            $msg_files = TempFileTable::copyFilesByRequest($request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

            $report->setAgreementCommentsFile('');
            if (!empty($msg_files)) {
                $report->setAgreementCommentsFile($msg_files[0]);
            }

            $report->setStatus('wait_specialist');
            $report->save();

            $message = $this->addMessageToDiscussion($model, 'Отчёт на проверке дизайнера.');
            $msg_comment = null;
            if ($status != 'accept') {
                $discussionLabel = 'Отчет не согласован. Внесите комментарии.';
                $this->addMessageToDiscussion(
                    $model,
                    $discussionLabel,
                    false
                );

                if (!empty($agreement_comments)) {
                    $message = $this->addMessageToDiscussion($model, 'Комментарий менеджера. ' . (!empty($agreement_comments) ? $agreement_comments . '.' : ''), false);
                }
            }

            $this->addFileToSpecialistMessage($message, $report->getAgreementCommentsFile(), AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);
            $entry = LogEntryTable::getInstance()->addEntry(
                $this->getUser()->getAuthUser(),
                $model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
                'sent_to_specialist',
                $model->getActivity()->getName() . '/' . $model->getName(),
                'Отчёт отправлен специалистам',
                '',
                $model->getDealer(),
                $model->getId(),
                'agreement'
            );

            if ($status != 'accept') {
                AgreementDealerHistoryMailSender::send('AgreementReportDeclinedMail', $entry, $model->getDealer(), null, false);
            }
        }

        foreach ($data['group'] as $group_id => $true) {
            $specialist = $this->getSpecialist($data, $group_id);
            //$msg = $this->getMessageForSpecialist($data, $group_id);
            $this->sendReportToSpecialist($report, $specialist, $agreement_comments);
        }

        return $this->sendFormBindResult($form, 'window.accept_report_form.onResponse');
    }

    protected function sendReportToSpecialist(AgreementModelReport $report, User $specialist, $msg)
    {
        $comment = new AgreementModelReportComment();
        $comment->setArray(array(
            'report_id' => $report->getId(),
            'user_id' => $specialist->getId()
        ));

        $comment->setStatus('wait');
        $comment->save();

        $model = $report->getModel();

        //$this->sendMessageToSpecialist($model->getDiscussion(), $specialist, $msg ?: 'Отправлено для согласования', false);

        $log_entry = LogEntryTable::getInstance()->addEntry(
            $this->getUser()->getAuthUser(),
            $model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
            'sent_to_specialist',
            $model->getActivity()->getName() . '/' . $model->getName(),
            'Вам отправлен отчёт для согласования',
            '',
            $model->getDealer(),
            $model->getId(),
            'agreement'
        );
        $log_entry->setPrivateUser($specialist);
        $log_entry->save();

        AgreementSpecialistHistoryMailSender::send('AgreementReportSentToSpecialistMail', $log_entry, $specialist, $msg);
    }

    protected function sendMessageToSpecialist(Discussion $discussion, User $specialist, $msg, $msg_show = true)
    {
        $owner = $this->getUser()->getAuthUser();

        $message = new Message();
        $message->setDiscussion($discussion);
        $message->setUser($owner);
        $message->setUserName($owner->selectName());
        $message->setText('>>> ' . $specialist->selectName() . "\r\n" . $msg);
        $message->setPrivateUser($specialist);
        $message->setSystem(true);
        $message->setMsgShow($msg_show);
        $message->save();

        // приватное сообщение себе
        $message = new Message();
        $message->setDiscussion($discussion);
        $message->setUser($owner);
        $message->setUserName($owner->selectName());
        $message->setText('>>> ' . $specialist->selectName() . "\r\n" . $msg);
        $message->setPrivateUser($owner);
        $message->setSystem(true);
        $message->setMsgShow($msg_show);
        $message->save();

        $last_read = $discussion->getLastRead($owner);
        $last_read->setMessageId($message->getId());
        $last_read->save();
    }

    protected function getSpecialist($data, $group_id)
    {
        if (!isset($data['user'][$group_id]))
            throw new NotFoundSpecialistForGroupException($group_id);

        $specialist = UserTable::getInstance()->createQuery()->where('id=? and group_id=?', array($data['user'][$group_id], $group_id))->fetchOne();
        if (!$specialist)
            throw new SpecialistNotFoundException($group_id, $data['user'][$group_id]);

        if (!$specialist->getGroup()->isSpecialist())
            throw new UserIsNotSpecialistException($specialist);

        return $specialist;
    }

    protected function getMessageForSpecialist($data, $group_id)
    {
        return isset($data['msg']) && is_array($data['msg']) && isset($data['msg'][$group_id])
            ? $data['msg'][$group_id]
            : '';
    }

    protected function getSpecialistData(sfWebRequest $request)
    {
        $data = $request->getPostParameter('specialist', array());
        if (!isset($data['group']) || !isset($data['user']) || !is_array($data['group']) || !is_array($data['user'])) {
            //throw new BadSpecialistsFormatException();
            return null;
        }

        return $data;
    }

    function outputModels(sfWebRequest $request)
    {
        $this->models = $this->loadModelsList($request);
    }

    function loadModelsList(sfWebRequest $request)
    {
        $sorts = array(
            'id' => 'm.id',
            'dealer' => 'm.dealer_id', // сортировка по id дилеров (фактически - это группировка)
            'name' => 'm.name',
            'cost' => 'm.cost'
        );

        $sort_column = $this->getSortColumn();
        $sort_direct = $this->getSortDirection();


        $sql_sort = 'm.id';
        if (isset($sorts[$sort_column]))
            $sql_sort = $sorts[$sort_column] . ' ' . ($sort_direct ? 'DESC' : 'ASC');

        $modelType = $this->getModelTypeFilter();

        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->select('m.*, r.status, mc.status, v.*')
            ->innerJoin('m.Activity a')
            ->innerJoin('m.ModelType mt WITH mt.concept=?', false)
            //->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r')
            ->leftJoin('m.Comments mc')
            ->leftJoin('m.Values v')
            //->where('m.step2 = ? or m.step2 = ?', array('none', 'accepted'))
            ->orderBy($sql_sort);

        if ($this->getModelFilter()) {
            $query->andWhere('m.id=?', $this->getModelFilter());
        } else {
            switch ($this->getWaitFilter()) {
                case 'specialist':
                    $query->andWhere('m.wait_specialist=?', true);
                    break;
                case 'dealer':
                    //$query->andWhere('m.status=? or r.status=?', array('declined', 'declined'));
                    $query->andWhere('(m.status=? or r.status=?)', array('not_sent', 'not_sent'));
                    break;
                case 'manager':
                    //$query->andWhere('m.wait=?', true);
                    $query->andWhere('(m.status=? or r.status=?)', array('wait', 'wait'));
                    break;
                case 'agreed':
                    $query->andWhere('(m.status=? and r.status=?)', array('accepted', 'accepted'));
                    break;
            }

            if ($this->getWaitFilter() == "blocked") {
                $query->andWhere('m.is_blocked = ? and m.allow_use_blocked = 0', true);
            } else {
                $query->andWhere('m.is_blocked = ? or (m.is_blocked = ? and m.allow_use_blocked = ?)', array(false, true, true));
            }

            if ($this->getDealerFilter()) {
                $query->andWhere('m.dealer_id=?', $this->getDealerFilter()->getId());
            }

            if ($this->getActivityFilter()) {
                $query->andWhere('m.activity_id = ?', $this->getActivityFilter()->getId());
            }

            if ($this->getStartDateFilter()) {
                $query->andWhere('m.created_at>=?', D::toDb($this->getStartDateFilter()));
            }

            if ($this->getEndDateFilter()) {
                $query->andWhere('m.created_at<=?', D::toDb($this->getEndDateFilter()));
            }

            /*if (!$this->getStartDateFilter() && !$this->getEndDateFilter()) {
                $query->andWhere('year(m.created_at) = ? or year(m.updated_at) = ?', array($this->getYearFilter($request), $this->getYearFilter($request)));
            }*/

            $modelStatus = $this->getModelStatusFilter();
            if ($modelStatus && $modelStatus != 'all') {
                if ($modelStatus == 'accepted') {
                    $query->andWhere('m.status = ? and r.status = ?', array('accepted', 'accepted'));
                } else if ($modelStatus == 'wait') {
                    $query->andWhere('m.wait_specialist = ?', 1);
                } else if ($modelStatus == 'comment') {
                    $query->andWhere('mc.status = ?', array('wait'))
                        ->andWhere('m.agreement_comments IS NOT NULL');
                    //$query->andWhere('mc.status = ?', 'wait');
                }
            }

            if ($modelType && $modelType == 'all') {
                $query->orWhere('r.status = ?', array('wait_specialist'));
            } else {
                if ($modelType && $modelType != 'all') {
                    if ($modelType == 'makets') {
                        $query->andWhere('m.status = ?', array('wait'));
                    } else if ($modelType == 'reports') {
                        $query->andWhere('(r.status = ? or r.status = ?) and m.status = ?', array('wait', 'wait_specialist', 'accepted'));
                    }
                }
            }

        }

        $mods = array();

        $this->models = $query->execute();
        foreach ($this->models as $m) {
            if ($this->getWaitFilter() == 'blocked') {
                $date = $m->getUpdatedAt();
            } else {
                if ($this->getWaitFilter() == 'agreed' || $this->getWaitFilter() == 'all') {
                    $date = $m->getUpdatedAt();
                } else {
                    $date = $m->getModelAcceptToDate(($this->getWaitFilter() && $this->getWaitFilter() == 'manager') ? false : true);

                    $date = date('d-m-Y H:i:s', strtotime($date) + mt_rand(1, 60));
                    $dateTime = D::toUnix($date);

                    if (array_key_exists($dateTime, $mods)) {
                        $date = date('d-m-Y H:i:s', $dateTime + mt_rand(1, 60));
                    }
                }
            }

            $mods[D::toUnix($date)] = $m;
        }

        if ($this->getWaitFilter() != 'blocked') {
            ksort($mods, SORT_NUMERIC);
        }

        return $mods;
    }

    function executeLoadModelsByAjax(sfWebRequest $request)
    {
        $this->models = null;
        /*if(!$this->getModelFilter()) {
            $this->outputFilter();

          if($this->getDesignerFilter())
            $this->models = $this->loadDesignerModels($request);
          else
              $this->models = $this->loadModelsList($request);
        }*/
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

        $query->andWhere('m.dealer_id=?', $this->getUser()->getAuthUser()->getDealer()->getId());

        if ($this->getActivityStatusFilter()) {
            switch ($this->getActivityStatusFilter()) {
                case 'in_work':
                    $query->andWhere('m.wait_specialist = ?', 1);
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
            }
        } else {
            $query->andWhere('m.status = ? or m.status = ? or m.status = ?', array('declined', 'not_sent', 'accepted'))
                ->andWhere('m.report_id is null');
        }

        if ($this->getStartDateFilter())
            $query->andWhere('m.created_at>=?', D::toDb($this->getStartDateFilter()));
        if ($this->getEndDateFilter())
            $query->andWhere('m.created_at<=?', D::toDb($this->getEndDateFilter()));

        $this->models = $query->execute();

        $mods = array();
        foreach ($this->models as $m) {
            $mods[strtotime($m->getModelAcceptToDate())] = $m;
        }

        ksort($mods, SORT_NUMERIC);

        $this->models = $mods;
    }

    function loadDesignerModels(sfWebRequest $request)
    {
        $sorts = array(
            'id' => 'm.id',
            'dealer' => 'm.dealer_id', // сортировка по id дилеров (фактически - это группировка)
            'name' => 'm.name',
            'cost' => 'm.cost'
        );

        $sort_column = $this->getSortColumn();
        $sort_direct = $this->getSortDirection();

        $sql_sort = 'm.id';

        if (isset($sorts[$sort_column]))
            $sql_sort = $sorts[$sort_column] . ' ' . ($sort_direct ? 'DESC' : 'ASC');

        /*$query = AgreementModelTable::getInstance()
                 ->createQuery('m')
                 ->innerJoin('m.Activity a')
                 ->innerJoin('m.ModelType mt WITH mt.concept=?', false)
                 ->leftJoin('m.Discussion d')
                 ->leftJoin('m.Report r')
                 ->orderBy($sql_sort);*/

        $designer_id = $this->getDesignerFilter();
        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activity a')
            ->innerJoin('m.ModelType mt WITH mt.concept=?', false)
            ->leftJoin('m.Comments mc')
            //->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r')
            ->leftJoin('r.Comments rc')
            ///->leftJoin('m.Dealer dealer')
            //->where('(mc.user_id=? and mc.status=?) or (rc.user_id=? and rc.status=?)', array($user->getId(), 'wait', $user->getId(), 'wait'));
            ->where('(mc.user_id=?) or (rc.user_id=?)', array($designer_id->getId(), $designer_id->getId()))
            ->orderBy($sql_sort);;

        if ($this->getModelFilter()) {
            $query->andWhere('m.id=?', $this->getModelFilter());
        } else {
            switch ($this->getWaitFilter()) {
                /*case 'specialist':
                  $query->andWhere('m.wait_specialist=?', true);
                  break;
                case 'dealer':
                  $query->andWhere('m.status=? or r.status=?', array('declined', 'declined'));
                  break;
                case 'manager':
                  $query->andWhere('m.wait=?', true);
                  break;
                case 'agreed':
                  $query->andWhere('m.status=? and r.status=?', array('accepted', 'accepted'));
                  break;*/
                case 'specialist':
                    $query->andWhere('m.wait_specialist=?', true);
                    break;
                case 'dealer':
                    //$query->andWhere('m.status=? or r.status=?', array('declined', 'declined'));
                    $query->andWhere('m.status=? or r.status=?', array('not_sent', 'not_sent'));
                    break;
                case 'manager':
                    //$query->andWhere('m.wait=?', true);
                    $query->andWhere('m.status=? or r.status=?', array('wait', 'wait'));
                    break;
                case 'agreed':
                    $query->andWhere('m.status=? and r.status=?', array('accepted', 'accepted'));
                    break;
            }

            if ($this->getDealerFilter())
                $query->andWhere('m.dealer_id=?', $this->getDealerFilter()->getId());

            if ($this->getActivityFilter())
                $query->andWhere('m.activity_id = ?', $this->getActivityFilter()->getId());

            if ($this->getStartDateFilter())
                $query->andWhere('m.created_at>=?', D::toDb($this->getStartDateFilter()));
            if ($this->getEndDateFilter())
                $query->andWhere('m.created_at<=?', D::toDb($this->getEndDateFilter()));

            if (!$this->getStartDateFilter() && !$this->getEndDateFilter()) {
                $query->andWhere('year(m.created_at) = ? or year(m.updated_at) = ?', array($this->getYearFilter($request), $this->getYearFilter($request)));
            }

            $modelStatus = $this->getModelStatusFilter();
            if ($modelStatus && $modelStatus != 'all') {
                if ($modelStatus == 'accepted') {
                    $query->andWhere('m.status = ? and m.report_id is null', 'accepted');
                } else if ($modelStatus == 'wait') {
                    $query->andWhere('m.wait_specialist = ?', 1);
                } else if ($modelStatus == 'comment') {
                    //$query->andWhere('m.status = ? or m.status = ?', array('wait_specialist', 'declined'));
                    $query->andWhere('m.status = ?', array('declined'));
                    $query->andWhere('m.agreement_comments is not null');
                    //$query->andWhere('mc.status = ?', 'wait');
                }

            }

            $offset = $this->getModelsFilterByOffset();
            $query->limit(self::LIMIT_MODELS_COUNT);
            if ($offset != 0) {
                $query->offset($offset * self::LIMIT_MODELS_COUNT);
            }
        }

        $this->models = $query->execute();
        if (count($this->models) == 0)
            $this->setModelFilterOffsetTo(--$offset);

        $mods = array();
        foreach ($this->models as $m) {
            $mods[strtotime($m->getModelAcceptToDate(false))] = $m;
        }

        ksort($mods, SORT_NUMERIC);
        $this->models = $mods;

        return $this->models;
    }

    function outputDesignerModels(sfWebRequest $request)
    {
        $this->models = $this->loadDesignerModels($request);
    }

    function outputDeclineReasons()
    {
        $this->decline_reasons = AgreementDeclineReasonTable::getInstance()->createQuery()->execute();
    }

    function outputDeclineReportReasons()
    {
        $this->decline_report_reasons = AgreementDeclineReportReasonTable::getInstance()->createQuery()->execute();
    }

    function outputSpecialistGroups()
    {
        $this->specialist_groups = UserGroupTable::getInstance()
            ->createQuery('g')
            ->distinct()
            ->select('g.*')
            //->innerJoin('g.Roles r WITH r.role=?', 'specialist')
            ->innerJoin('g.Roles r')
            ->innerJoin('g.Users u WITH u.active=?', true)
            ->whereIn('g.id', array(UserGroup::ADMINISTRATOR, UserGroup::IMPORTER, UserGroup::DESIGNER))
            ->execute();
    }

    function outputFilter()
    {
        $this->outputWaitFilter();

        $this->outputDealers();
        $this->outputActivities();
        $this->outputDesigners();

        $this->outputDealerFilter();
        $this->outputActivityFilter();
        $this->outputStartDateFilter();
        $this->outputEndDateFilter();
        $this->outputModelFilter();
        $this->outputModelTypeFilter();

        $this->outputDesignerFilter();
        $this->outputDesignerModelstatusFilter();

        $this->outputActivitystatusFilter();

        //$this->getModelsFilterByOffset();
    }

    function outputWaitFilter()
    {
        $this->wait_filter = $this->getWaitFilter();
    }

    function outputDealerFilter()
    {
        $this->dealer_filter = $this->getDealerFilter();
    }

    function outputActivityFilter()
    {
        $this->activity_filter = $this->getActivityFilter();
    }

    function outputDesignerFilter()
    {
        $this->designer_filter = $this->getDesignerFilter();
    }

    function outputDesignerModelstatusFilter()
    {
        $this->model_status_filter = $this->getModelStatusFilter();
    }

    function outputStartDateFilter()
    {
        $this->start_date_filter = $this->getStartDateFilter();
    }

    function outputEndDateFilter()
    {
        $this->end_date_filter = $this->getEndDateFilter();
    }

    function outputModelFilter()
    {
        $this->model_filter = $this->getModelFilter();
    }

    function outputDealers()
    {
        $this->dealers = DealerTable::getVwDealersQuery()->execute();
    }

    function outputActivities()
    {
        $this->activities = $this->getActivities(false);
    }

    function outputFinishedActvities()
    {
        $this->finishedActivities = $this->getActivities(true);
    }

    function getActivities($finished = false)
    {
        return ActivityTable::getInstance()
            ->createQuery()
            //->where('year(updated_at) = ? and finished = ?', array(date('Y'), $finished))
            ->where('finished = ?', array($finished))
            ->orderBy('id ASC')
            ->execute();
    }

    function outputModelTypeFilter()
    {
        $this->model_type_filter = $this->getModelTypeFilter();
    }

    function outputActivitystatusFilter()
    {
        $this->activity_status = $this->getActivityStatusFilter();
    }

    function outputDesigners()
    {
        $this->designers = UserTable::getInstance()
            ->createQuery()
            ->where('group_id = ?', 22)
            ->andWhere('active = ?', true)
            ->orderBy('name ASC')
            ->execute();
    }

    function outputConcepts(sfWebRequest $request)
    {
        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activity a')
            ->innerJoin('m.ModelType mt WITH mt.concept=?', true)
            ->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r')
            ->orderBy('m.id desc');

        if ($this->getModelFilter()) {
            $query->andWhere('m.id=?', $this->getModelFilter());
        } else {
            switch ($this->getWaitFilter()) {
                case 'specialist':
                    $query->andWhere('m.wait_specialist=?', true);
                    break;
                case 'dealer':
                    $query->andWhere('m.status=? or r.status=?', array('declined', 'declined'));
                    break;
                case 'manager':
                    $query->andWhere('m.wait=?', true);
                    break;
                case 'agreed':
                    $query->andWhere('m.status=? and r.status=?', array('accepted', 'accepted'));
                    break;
            }

            if ($this->getDealerFilter())
                $query->andWhere('m.dealer_id=?', $this->getDealerFilter()->getId());

            if ($this->getStartDateFilter())
                $query->andWhere('m.created_at>=?', D::toDb($this->getStartDateFilter()));
            if ($this->getEndDateFilter()) {
                $query->andWhere('m.created_at<=?', D::toDb($this->getEndDateFilter()));
            }

            if (!$this->getStartDateFilter() && !$this->getEndDateFilter()) {
                $query->andWhere('(year(m.created_at) = ? or year(m.updated_at) = ? or year(m.created_at) = ? or year(m.updated_at) = ?)',
                    array
                    (
                        date('Y'), date('Y'), date('Y') - 1, date('Y') - 1
                    )
                );
            }
        }

        $this->concepts = $query->execute();
    }

    protected function attachAgreementModelCommentsFileToMessage(AgreementModel $model, Message $message)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile($message->getId() . '-' . $model->getAgreementCommentsFile());

        copy(
            sfConfig::get('sf_upload_dir') . '/' . AgreementModel::AGREEMENT_COMMENTS_FILE_PATH . '/' . $model->getAgreementCommentsFile(),
            sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
        );

        $file->save();
    }

    protected function attachAgreementModelReportCommentsFileToMessage(AgreementModelReport $report, Message $message)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile($message->getId() . '-' . $report->getAgreementCommentsFile());

        copy(
            sfConfig::get('sf_upload_dir') . '/' . AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH . '/' . $report->getAgreementCommentsFile(),
            sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
        );

        $file->save();
    }

    /**
     * Add message to discussion
     *
     * @param AgreementModel $model
     * @param string $text
     * @return Message|false
     */
    protected function addMessageToDiscussion(AgreementModel $model, $text, $msg_show = true)
    {
        $discussion = $model->getDiscussion();

        if (!$discussion) {
            return;
        }

        $message = new Message();
        $user = $this->getUser()->getAuthUser();
        $message->setDiscussionId($discussion->getId());
        $message->setUser($user);
        $message->setUserName($user->selectName());
        $message->setText($text);
        $message->setSystem(true);
        $message->setMsgShow($msg_show);
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

    /**
     * Returns model
     *
     * @param sfWebRequest $request
     * @return AgreementModel|false
     */
    function getModel(sfWebRequest $request)
    {
        return AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activity a')
            ->leftJoin('m.Report r')
            ->where('m.status<>? and m.id=?', array('not_send', $request->getParameter('id')))
            ->fetchOne();
    }

    function getWaitFilter()
    {
        $default = $this->getUser()->getAttribute('wait', 'manager', self::FILTER_NAMESPACE);
        $wait = $this->getRequestParameter('wait', $default);

        if ($wait != 'manager' && $wait != "all" && $wait != "specialist" && $wait != 'dealer') {
            $this->getUser()->setAttribute('wait', $wait, self::FILTER_NAMESPACE);
            $this->resetFilters();

            //$this->redirect('@agreement_module_management_models?wait='.$wait);
        } else if ($wait == 'manager') {
            $this->getUser()->setAttribute('designer_id', 0, self::FILTER_NAMESPACE);
        }

        $this->getUser()->setAttribute('wait', $wait, self::FILTER_NAMESPACE);

        return $wait;
    }

    /**
     * Returns dealer
     *
     * @return Dealer|null
     */
    function getDealerFilter()
    {
        if ($this->_dealer_filter === null) {
            $default = $this->getUser()->getAttribute('dealer_id', 0, self::FILTER_NAMESPACE);
            $id = $this->isReset ? $default : $this->getRequestParameter('dealer_id', $default);

            $this->getUser()->setAttribute('dealer_id', $id, self::FILTER_NAMESPACE);

            $this->_dealer_filter = $id ? DealerTable::getInstance()->find($id) : false;
        }

        return $this->_dealer_filter;
    }

    function getActivityFilter()
    {
        if ($this->_activity_filter === null) {
            $default = $this->getUser()->getAttribute('activity_id', 0, self::FILTER_NAMESPACE);
            $id = $this->isReset ? $default : $this->getRequestParameter('activity_id', $default);

            $this->getUser()->setAttribute('activity_id', $id, self::FILTER_NAMESPACE);

            $this->_activity_filter = $id ? ActivityTable::getInstance()->find($id) : false;
        }

        return $this->_activity_filter;
    }

    function getDesignerFilter()
    {
        if ($this->_designer_filter === null) {
            $default = $this->getUser()->getAttribute('designer_id', 0, self::FILTER_NAMESPACE);
            $id = $this->isReset ? $default : $this->getRequestParameter('designer_id', $default);

            if ($id == -1) {
                $this->_designer_filter = null;

                return $this->_designer_filter;
            }

            $this->getUser()->setAttribute('designer_id', $id, self::FILTER_NAMESPACE);

            $this->_designer_filter = $id ? UserTable::getInstance()->find($id) : false;
        }
        return $this->_designer_filter;
    }

    function getStartDateFilter()
    {
        return $this->getDateFilter('start_date');
    }

    function getEndDateFilter()
    {
        return $this->getDateFilter('end_date');
    }

    function getModelsFilterByOffset()
    {
        $offset = $this->getUser()->getAttribute('models_offset', -1, self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('models_offset', ++$offset, self::FILTER_NAMESPACE);

        return $offset;
    }

    function setModelFilterOffsetTo($offset)
    {
        $this->getUser()->setAttribute('models_offset', $offset, self::FILTER_NAMESPACE);
    }

    function resetModelFilterByOffset()
    {
        $this->getUser()->setAttribute('models_offset', -1, self::FILTER_NAMESPACE);

    }

    function getModelStatusFilter()
    {
        $default = $this->getUser()->getAttribute('model_status', 'all', self::FILTER_NAMESPACE);
        $model_status = $this->isReset ? $default : $this->getRequestParameter('model_status', $default);
        $this->getUser()->setAttribute('model_status', $model_status, self::FILTER_NAMESPACE);

        return $model_status;
    }

    function getModelFilter()
    {
        $default = $this->getUser()->getAttribute('model', '', self::FILTER_NAMESPACE);
        $model_id = $this->isReset ? $default : $this->getRequestParameter('model', $default);
        $this->getUser()->setAttribute('model', $model_id, self::FILTER_NAMESPACE);

        return $model_id;
    }

    function getActivityStatusFilter()
    {
        $default = $this->getUser()->getAttribute('activity_status', '', self::FILTER_NAMESPACE);
        $status = $this->isReset ? $default : $this->getRequestParameter('activity_status', $default);
        $this->getUser()->setAttribute('activity_status', $status, self::FILTER_NAMESPACE);

        return $status;
    }

    function getModelTypeFilter()
    {
        $default = $this->getUser()->getAttribute('model_type', '', self::FILTER_NAMESPACE);
        $status = $this->isReset ? $default : $this->getRequestParameter('model_type', $default);
        $this->getUser()->setAttribute('model_type', $status, self::FILTER_NAMESPACE);

        return $status;
    }

    function getYearFilter($request)
    {
        $this->year = D::getBudgetYear($request);
        $this->budgetYears = D::getBudgetYears($request);

        $default = $this->getUser()->getAttribute('year', date('Y'), self::FILTER_NAMESPACE);
        $this->year = $this->isReset ? $default : $this->getRequestParameter('year', $default);
        $this->getUser()->setAttribute('year', $this->year, self::FILTER_NAMESPACE);

        return $this->year;
    }

    protected function getDateFilter($name)
    {
        $default = $this->getUser()->getAttribute($name, '', self::FILTER_NAMESPACE);
        $date = $this->isReset ? $default : $this->getRequestParameter($name, $default);
        $this->getUser()->setAttribute($name, $date, self::FILTER_NAMESPACE);

        return preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$#', $date)
            ? D::fromRus($date)
            : false;
    }

    function resetFilters()
    {
        //$this->getUser()->setAttribute('models_offset', -1, self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('start_date', '', self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('end_date', '', self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('model_type', '', self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('model', '', self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('model_status', 'all', self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('designer_id', 0, self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('dealer_id', 0, self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('activity_id', 0, self::FILTER_NAMESPACE);

        $this->isReset = true;
    }

    function executeUnblock(sfWebRequest $request)
    {
        $modelId = $request->getParameter('modelId');

        $model = AgreementModelTable::getInstance()->find($modelId);

        if ($model) {
            $model->setAllowUseBlocked(true);
            $model->setUseBlockedTo(D::toDb(strtotime('+3 day', strtotime(date('d-m-Y H:i:s'))), true));
            $model->save();

            $reportId = $model->getReport() ? $model->getReport()->getId() : null;
            AgreementModelTable::addModelBlockedStatistic($model->getId(), $reportId, AgreementModelTable::MODEL_BLOCKED_ACTIVE, $this->getUser()->getAuthUser()->getId());

            $dealer_users = UserTable::getInstance()
                ->createQuery('u')
                ->innerJoin('u.DealerUsers du WITH dealer_id=?', $model->getDealerId())
                ->where('active=?', true)
                ->groupBy('du.dealer_id')
                ->execute();

            foreach ($dealer_users as $user) {
                $message = new AgreementDealerModelBlockInform($user, $model, 'unblock');
                $message->setPriority(1);

                sfContext::getInstance()->getMailer()->send($message);
            }

            return $this->sendJson(array('success' => true));
        }

        return $this->sendJson(array('success' => false));
    }

    //Favorites reports

    function executeReportFileAddToFavorites(sfWebRequest $request)
    {
        $item = new AgreementModelReportFavorites();

        $item->setReportId($request->getParameter('modelReportId'));
        $item->setFileName($request->getParameter('fileName'));
        $item->setFileId($request->getParameter('fileInd'));
        $item->setReportModelTypeId($request->getParameter('typeId'));
        $item->setUserId($this->getUser()->getAuthUser()->getId());

        $item->save();

        return $this->sendJson(array('file' => $request->getParameter('fileName'), 'success' => true, 'fileInd' => $request->getParameter('fileInd')));
    }

    function executeReportFileRemoveFromFavorites(sfWebRequest $request)
    {
        $item = AgreementModelReportFavoritesTable::getInstance()
            ->createQuery()
            ->select()
            //->where('report_id = ? and file_name = ? and user_id = ?',
            ->where('report_id = ? and file_name = ?',
                array(
                    $request->getParameter('modelReportId'),
                    $request->getParameter('fileName'),
                    //$this->getUser()->getAuthUser()->getId()
                )
            )
            ->fetchOne();
        if ($item) {
            $item->delete();

            return $this->sendJson(array('file' => $request->getParameter('fileName'), 'success' => true, 'fileInd' => $request->getParameter('fileInd')));
        }

        return $this->sendJson(array('success' => false));
    }

    function outputFavoritesReports()
    {

        $query = AgreementModelReportFavoritesTable::getInstance()
            ->createQuery('f')
            ->select('f.*, r.*, m.*, log_entry.created_at as report_added')
            ->innerJoin('f.Report r')
            ->innerJoin('r.Model m')
            ->innerJoin('m.Activity a')
            ->innerJoin('m.LogEntry log_entry')
            ->andWhere('log_entry.action = ? and log_entry.object_type = ? and log_entry.private_user_id = ?', array('edit', 'agreement_report', 0))
            ->orderBy('f.id DESC');

        if ($this->getFavoritesActivity()) {
            $query->andWhere('a.id = ?', $this->getFavoritesActivity()->getId());
        }
        else if ($this->getFavoritesFinishedActivity()) {
            $query->andWhere('a.id = ?', $this->getFavoritesFinishedActivity()->getId());
        }

        if ($this->getFavoritesDealer()) {
            $query->andWhere('m.dealer_id = ?', $this->getFavoritesDealer()->getId());
        }

        if ($this->getFavoritesModelType()) {
            $query->andWhere('m.model_type_id = ?', $this->getFavoritesModelType()->getId());
        }

        if ($this->getFavoritesStartDateFilter() || $this->getFavoritesStartDateFilter()) {
            if ($this->getFavoritesStartDateFilter()) {
                $query->andWhere('log_entry.created_at >= ?', D::toDb($this->getFavoritesStartDateFilter()));
            }

            if ($this->getFavoritesEndDateFilter()) {
                $query->andWhere('log_entry.created_at <= ?', D::toDb($this->getFavoritesEndDateFilter()));
            }
        }

        $this->initPager($query, 'AgreementModelReportFavorites', 50);
        $this->initPaginatorData(null, 'favorites_reports');

        $this->favorites = $query->execute();

        return $this->favorites;
    }

    private function initPager($query, $object = 'AgreementModel', $items_per_page = -1)
    {
        if ($items_per_page == -1) {
            $items_per_page = sfConfig::get('app_max_models_on_page');
        }

        $request = $this->getRequest();
        $page = $request->getParameter('page', 1);
        if ($page) {
            $max_items_on_page = $items_per_page;
        } else {
            $max_items_on_page = 0;
            $page = 1;
        }

        $this->page = $page;

        $this->pager = new sfDoctrinePager(
            $object,
            $max_items_on_page
        );

        $this->pager->setQuery($query);
        $this->pager->setPage($page);
        $this->pager->init();

        if ($this->pager->getLastPage() < $page) $this->pager->setPage($this->pager->getLastPage());
        $this->pager->init();
    }

    private function initPaginatorData($route_object, $route_name)
    {
        $request = $this->getRequest();
        $this->parameters = $request->getGetParameters();
        $this->pageLinkArray = array_merge($this->parameters, array('sf_subject' => $route_object));

        $this->paginatorData = array('pager' => $this->pager,
            'pageLinkArray' => $this->pageLinkArray,
            'route' => $route_name);
    }

    function executeFavoritesReports(sfWebRequest $request)
    {
        $this->getYearFilter($request);

        $this->outputDeclineReasons();
        $this->outputDeclineReportReasons();
        $this->outputSpecialistGroups();

        $this->outputFavortiesReportsFilters();

        $this->outputFavoritesReports();
    }

    function outputModelTypes()
    {
        $this->modelTypes = AgreementModelTypeTable::getInstance()
            ->createQuery()
            ->select()
            ->orderBy('id ASC')
            ->execute();
    }

    function outputFavortiesReportsFilters()
    {

        $this->outputDealers();
        $this->outputActivities();
        $this->outputFinishedActvities();
        $this->outputModelTypes();

        $this->outputFavoritesActivityFilter();
        $this->outputFavoritesFinishedActivityFilter();
        $this->outputFavoritesDealerFilter();
        $this->outputFavortiesDatesFilter();
        $this->outputFavoritesModelTypeFilter();
    }

    function outputFavoritesActivityFilter()
    {
        $this->favorites_activity_filter = $this->getFavoritesActivity();
    }

    function outputFavoritesFinishedActivityFilter()
    {
        $this->favorites_activity_finished_filter = $this->getFavoritesFinishedActivity();
    }

    function outputFavoritesDealerFilter()
    {
        $this->favorites_dealer_filter = $this->getFavoritesDealer();
    }

    function outputFavortiesDatesFilter()
    {
        $this->favorites_start_date_filter = $this->getFavoritesStartDateFilter();
        $this->favorites_end_date_filter = $this->getFavoritesEndDateFilter();
    }

    function outputFavoritesModelTypeFilter()
    {
        $this->favorites_model_type_filter = $this->getFavoritesModelType();
    }

    private function getFavoritesActivity()
    {
        if ($this->_favorites_activity_filter === null) {
            $default = $this->getUser()->getAttribute('activity_id', 0, self::FILTER_NAMESPACE_FAVORITES);
            $id = $this->isReset ? $default : $this->getRequestParameter('activity_id', $default);

            $this->getUser()->setAttribute('activity_id', $id, self::FILTER_NAMESPACE_FAVORITES);

            $this->_favorites_activity_filter = $id ? ActivityTable::getInstance()->find($id) : false;
        }
        return $this->_favorites_activity_filter;
    }

    private function getFavoritesFinishedActivity()
    {
        if ($this->_favorites_activity_finished_filter === null) {
            $default = $this->getUser()->getAttribute('finished_activity_id', 0, self::FILTER_NAMESPACE_FAVORITES);
            $id = $this->isReset ? $default : $this->getRequestParameter('finished_activity_id', $default);

            $this->getUser()->setAttribute('finished_activity_id', $id, self::FILTER_NAMESPACE_FAVORITES);

            $this->_favorites_activity_finished_filter = $id ? ActivityTable::getInstance()->find($id) : false;
        }
        return $this->_favorites_activity_finished_filter;
    }


    private function getFavoritesDealer()
    {
        if ($this->_favorites_dealer_filter === null) {
            $default = $this->getUser()->getAttribute('dealer_id', 0, self::FILTER_NAMESPACE_FAVORITES);
            $id = $this->getRequestParameter('dealer_id', $default);

            $this->getUser()->setAttribute('dealer_id', $id, self::FILTER_NAMESPACE_FAVORITES);

            $this->_favorites_dealer_filter = $id ? DealerTable::getInstance()->find($id) : false;
        }
        return $this->_favorites_dealer_filter;
    }

    private function getFavoritesModelType()
    {
        $this->_favorites_model_type_filter = null;
        if ($this->_favorites_model_type_filter === null) {
            $default = $this->getUser()->getAttribute('model_type', 0, self::FILTER_NAMESPACE_FAVORITES);
            $id = $this->getRequestParameter('model_type', $default);

            $this->getUser()->setAttribute('model_type', $id, self::FILTER_NAMESPACE_FAVORITES);

            $this->_favorites_model_type_filter = $id ? AgreementModelTypeTable::getInstance()->find($id) : false;
        }
        return $this->_favorites_model_type_filter;
    }

    private function getFavoritesStartDateFilter()
    {
        return $this->getFavoritesDateFilter('start_date');
    }

    private function getFavoritesEndDateFilter()
    {
        return $this->getFavoritesDateFilter('end_date');
    }

    protected function getFavoritesDateFilter($name)
    {
        $default = $this->getUser()->getAttribute($name, '', self::FILTER_NAMESPACE_FAVORITES);
        $date = $this->isReset ? $default : $this->getRequestParameter($name, $default);
        $this->getUser()->setAttribute($name, $date, self::FILTER_NAMESPACE_FAVORITES);

        return preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$#', $date)
            ? D::fromRus($date)
            : false;
    }

    function executeFavoritesAddToArchive(sfWebRequest $request)
    {
        $reports = $this->outputFavoritesReports();

        $zip = new ZipArchive();
        $zipFile = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'favorites.zip';

        @unlink($zipFile);
        $res = $zip->open($zipFile, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);

        if ($res) {
            $activityDirs = array();
            $dealersDirs = array();

            foreach ($reports as $item) {
                $report = $item->getReport();
                $model = $report->getModel();
                $dealer = $model->getDealer();
                $activity = $model->getActivity();

                $activityName = Utils::normalize($activity->getName());
                $dealerName = Utils::normalize($dealer->getName());

                $f = 'getAdditionalFile';
                if ($item->getFileIndex() != 0)
                    $f = 'getAdditionalFileExt' . $item->getFileIndex();

                $zip->addFile(sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . AgreementModelReport::ADDITIONAL_FILE_PATH . DIRECTORY_SEPARATOR . $report->$f(),
                    $activityName . DIRECTORY_SEPARATOR . $dealerName . DIRECTORY_SEPARATOR . $report->$f());

            }

            $res = $zip->close();
        }

        return $this->sendJson(array('url' => '/uploads/favorites.zip'));
    }

    function executeDeleteFavoritesItem(sfWebRequest $request)
    {
        $id = $request->getParameter('id');

        $item = AgreementModelReportFavoritesTable::getInstance()->find($id);
        if ($item) {
            $item->delete();

            return $this->sendJson(array('success' => true, 'id' => $id));
        }

        return $this->sendJson(array('success' => false));

    }

    function executeModelReportFileContainer(sfWebRequest $request)
    {
        $this->idx = $request->getParameter('fileIdx');
    }

    function executeFavoritesReportsExportToPdf(sfWebRequest $request)
    {
        $items = explode(':', $request->getParameter('items'));

        $totalItems = count($items);
        $itemIndex = 1;

        $htmlText = '';
        foreach ($items as $item) {
            $favItem = AgreementModelReportFavoritesTable::getInstance()->find($item);

            if ($favItem) {
                $model = $favItem->getReport()->getModel();

                $htmlText .= '<div style="width: 100%; height: 95%; float: left; ">';
                $htmlText .= '<h1 style="margin-left: 25px;">' . $model->getModelType()->getName() . '</h1>';
                $htmlText .= '<div style="padding: 10px; background: #ccc; height: 670px; padding: 10px;"><span style="font-size: 16px; font-weight: bold;">Активность:</span> ' . sprintf("%s [%s]", $model->getActivity()->getName(), $model->getActivity()->getId());
                $htmlText .= '<br/><span style="font-size: 16px; font-weight: bold;">' . $model->getName() . '</span>';
                $htmlText .= '<br/><span style="font-size: 16px; font-weight: bold;">Дилер:</span> ' . sprintf("%s [%s]", $model->getDealer()->getName(), $model->getDealer()->getShortNumber());

                $func = 'getAdditionalFile';
                if ($favItem->getFileIndex() != 0) {
                    $func = 'getAdditionalFileExt' . $favItem->getFileIndex();
                }

                $imageFile = sfconfig::get('sf_root_dir') . '/www/uploads/' . AgreementModelReport::ADDITIONAL_FILE_PATH . '/' . $favItem->getReport()->$func();
                $copyFile = $favItem->getReport()->$func();
                if (file_exists($imageFile)) {
                    $copyFileAr = F::imageResize($imageFile, 2024);

                    $copyFile = $copyFileAr['file'];
                    $maxW = $copyFileAr['aw'];
                    $maxH = $copyFileAr['ah'];
                }

                if (!is_null($copyFile)) {
                    $htmlText .= '<br/>';
                    $htmlText .= '<a target="_blank" href="' . sfConfig::get('app_site_url') . 'uploads/' . AgreementModelReport::ADDITIONAL_FILE_PATH . '/' . $copyFile . '">';

                    if ($maxW > $maxH) {
                        $htmlText .= '<img style="width: 650px; -moz-box-shadow: inset 3px 3px 3px rgba(0,0,0,0.1); -webkit-box-shadow: inset 3px 3px 3px rgba(0,0,0,0.1); box-shadow: inset 3px 3px 3px rgba(0,0,0,0.1); margin-top: 10px; text-align: center;" src="uploads/' . AgreementModelReport::ADDITIONAL_FILE_PATH . '/' . $copyFile . '"/>';
                    } else {
                        $htmlText .= '<img style="width: 400px; max-height: 600px; -moz-box-shadow: inset 3px 3px 3px rgba(0,0,0,0.1); -webkit-box-shadow: inset 3px 3px 3px rgba(0,0,0,0.1); box-shadow: inset 3px 3px 3px rgba(0,0,0,0.1); margin-top: 10px; text-align: center;" src="uploads/' . AgreementModelReport::ADDITIONAL_FILE_PATH . '/' . $copyFile . '"/>';
                    }
                    $htmlText .= '</a>';
                }

                $htmlText .= '</div';

                if ($itemIndex != $totalItems) {
                    $htmlText .= '<img style="page-break-after: always; margin-top: 5px; margin-right: 15px; float: right;" src="images/logo.png"/>';
                } else {
                    $htmlText .= '<img style="margin-top: 5px; margin-right: 15px; float: right;" src="images/logo.png"/>';
                }

                $htmlText .= '</div>';

                $itemIndex++;
            }
        }

        //$htmlText = iconv('UTF-8', 'windows-1251', $htmlText);

        $html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <style>
            * {
                font-family: times;
                line-height: 1em;
             }

             @page { margin: 0px !important; padding: 0px !important; }
        </style>
    </head>
 <body>
 ' . $htmlText . '
 </body>
</html>';

        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->set_paper(DOMPDF_DEFAULT_PAPER_SIZE, 'landscape');
        $dompdf->render();
        //$dompdf->stream(sfConfig::get('sf_root_dir')."/uploads/sample.pdf", array("Attachment" => true));
        $output = $dompdf->output();

        $fileName = "gen_" . rand(1, 10000) . ".pdf";
        $toFile = sfConfig::get('sf_root_dir') . "/www/uploads/pdf_gen/" . $fileName;
        file_put_contents($toFile, $output);

        return $this->sendJson(array('success' => true, 'fileUrl' => sfConfig::get('app_site_url') . "uploads/pdf_gen/" . $fileName));
    }

    public function executeModelsLoadDiscussionCount(sfWebRequest $request)
    {
        $models = AgreementModelTable::getInstance()
            ->createQuery()
            ->whereIn('id', explode(':', $request->getParameter('models')))
            ->execute();

        $designer_filter = $request->getParameter('designer_filter') == 1 ? true : false;

        $result = array();
        foreach ($models as $model) {
            $discussion = $model->getDiscussion();
            $new_messages_count = $discussion ? $discussion->countUnreadMessages($this->getUser()->getAuthUser()) : 0;

            $result[$model->getId()] = array('count' => $new_messages_count, 'designer_filter' => $model->isModelAcceptActiveToday($designer_filter));
        }

        return $this->sendJson(array('data' => $result, 'success' => count($result) > 0 ? true : false));
    }

    function getModelFiles(sfWebRequest $request, $update = false)
    {
        $files = $request->getFiles();
        if (!is_array($files)) {
            return $files;
        }

        $uploaded_files = $this->getUploadedFilesByField($files, $this->getModel($request), $update);
        if (!empty($uploaded_files)) {
            return $uploaded_files;
        }

        $server_file = $request->getPostParameter('server_model_file');
        if (!$server_file || preg_match('#[\\\/]#', $server_file)) {
            if (isset($files['agreement_comments_file']) && isset($files['agreement_comments_file'][0])) {
                return array('agreement_comments_file_1' => $files['agreement_comments_file'][0]);
            }

            return $files;
        }

        return array();
    }

    private function getUploadedFilesByField($files, $model = null, $update = false)
    {
        $fields = array('agreement_comments_file');

        $max_upload_files = sfConfig::get('app_max_files_upload_count');
        $ind = $file_ind = 1;
        $count_result = 0;

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
     * Download files by model and model file type
     * @param sfWebRequest $request
     * @throws sfStopException
     */
    public function executeDownloadAllFiles(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        $by_type = $request->getParameter('model_file_type');

        $this->redirect(ModelReportFiles::packUploadedFilesToZip($model, $by_type));
    }
}
