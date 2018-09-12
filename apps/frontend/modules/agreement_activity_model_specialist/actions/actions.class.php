<?php

include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel.php');
include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel/Cell.php');
include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel/Writer/Excel5.php');

ini_set('memory_limit', '1000M');

/**
 * agreement_activity_model_specialist actions.
 *
 * @package    Servicepool2.0
 * @subpackage agreement_activity_model_specialist
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agreement_activity_model_specialistActions extends ActionsWithJsonForm
{
    const SORT_ATTR = 'man_sort';
    const SORT_DIRECT_ATTR = 'man_sort_direct';
    const FILTER_NAMESPACE = 'agreement_filter';

    const DECLINE_MODEL_ACTION = 'decline_model';
    const DECLINE_REPORT_ACTION = 'decline_report';

    protected $_dealer_filter = null;

    private $uploaded_files_result = array();

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request)
    {
        $this->outputModels($request);
        $this->outputConcepts();
        $this->outputFilter();

    }

    public function executeUsers(sfWebRequest $request)
    {
        $this->outputUsersFilter();

        $this->outputUsers();


    }

    public function executeSearch(sfWebRequest $request)
    {

        $this->outputSearchResult($request);
        $this->outputFilter();
    }

    public function executeExcel()
    {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle('Users List');

        $this->outputUsers();

        $headers = array('Дилер', 'Группа', 'Email', 'Имя', 'Фамилия', 'Должность', 'Активен');
        $column = 0;
        $row = 0;

        //настройки для шрифтов
        $baseFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => false
            )
        );
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

        $aSheet->getColumnDimension('A')->setWidth(40);
        $aSheet->getColumnDimension('B')->setWidth(20);
        $aSheet->getColumnDimension('C')->setWidth(35);
        $aSheet->getColumnDimension('D')->setWidth(20);
        $aSheet->getColumnDimension('E')->setWidth(20);
        $aSheet->getColumnDimension('F')->setWidth(50);
        $aSheet->getColumnDimension('G')->setWidth(20);

        $row = 2;
        $column = 0;
        $tCount = 1;
        foreach ($this->users as $user) {
            $column = 0;

            $group = $user->getGroup();
            $roles = array(2, 3);

            if (!in_array($group->getId(), $roles))
                continue;

            $dealer = $user->getDealerUsers()->getFirst();
            if (empty($dealer))
                continue;

            $dealer = $dealer->getDealer();

            $aSheet->setCellValueByColumnAndRow($column++, $row, sprintf('%s (%s)', substr($dealer->getNumber(), 5), $dealer->getName()));
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getGroup()->getName());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getEmail());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getName());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getSurname());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getPost());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getActive() ? "Да" : "Нет");

            $aSheet->getStyle('A' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('B' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('C' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('D' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('E' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('F' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('G' . $tCount)->applyFromArray($center);

            $aSheet->getStyle('B' . $tCount . ':G' . $tCount)->applyFromArray($center);

            $row++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save(sfConfig::get('sf_root_dir') . '/www/uploads/users.xls');

        $this->redirect('http://dm.vw-servicepool.ru/uploads/users.xls');
        //return $sfView::NONE;
    }

    function executeDeleteUser(sfWebRequest $request)
    {
        $dealer_user = $this->getUser()->getAuthUser()->getDealerUsers()->getFirst();
        if (!$dealer_user)
            return $this->sendJson(array('success' => false, 'msg' => '1'));

        $this->dealer = $dealer_user->getDealer();
        if (!$this->dealer)
            return $this->sendJson(array('success' => false, 'msg' => '2'));

        $user = UserTable::getInstance()->find($request->getPostParameter('userId'));
        if (!$user)
            return $this->sendJson(array('success' => false, 'msg' => '3'));

        $dealer_user = $user->getDealerUsers()->getFirst();
        if (!$dealer_user || $dealer_user->getDealerId() != $this->dealer->getId())
            return $this->sendJson(array('success' => false, 'msg' => '4'));

        $user->delete();

        LogEntryTable::getInstance()->addEntry(
            $this->getUser()->getAuthUser(),
            'dealer_user',
            'delete',
            'Пользователи',
            'Удалён пользователь "' . $user->getEmail() . '"',
            '',
            $this->dealer,
            $user->getId()
        );

        return $this->sendJson(array('success' => true));
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

        $this->model = $model;
    }

    function executeReport(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        if (!$model)
            return sfView::ERROR;

        $this->report = $model->getReport();
    }

    function executeDeclineModel(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $form = new AgreementDeclineForm(array(), array(
            'comments_file_path' => AgreementModel::AGREEMENT_COMMENTS_FILE_PATH
        ));
        $form->bind(
            array(
                'agreement_comments' => $request->getPostParameter('agreement_comments')
            ),
            array()//$this->getCommentFiles($request)
        );

        if ($form->isValid()) {
            $this->setAcceptDecline($model, 'decline', false);

            $comment = $model->getSpecialistComment($this->getUser()->getAuthUser());
            $this->forward404Unless($comment);

            $discussion = $model->getDiscussion();
            if ($discussion) {
                $discussion->activeDisabledMessages($model->getManagerStatus() == 'accepted' ? true : false);
            }

            $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

            $utils = new AgreementModelStatusUtils();
            $utils->declineComment(
                $comment,
                $this->getUser()->getAuthUser(),
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

        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $form = new AgreementAcceptForm(array(), array(
            'comments_file_path' => AgreementModel::AGREEMENT_COMMENTS_FILE_PATH,
        ));

        $form->bind(
            array(
                'agreement_comments' => $request->getPostParameter('agreement_comments')
            ),
            array()//$this->getCommentFiles($request)
        );

        if ($form->isValid()) {
            $this->setAcceptDecline($model, 'accept', false);

            $comment = $model->getSpecialistComment($this->getUser()->getAuthUser());
            $this->forward404Unless($comment);

            $discussion = $model->getDiscussion();
            if ($discussion) {
                $discussion->activeDisabledMessages();
            }

            $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

            $utils = new AgreementModelStatusUtils();
            $utils->acceptComment($comment, $this->getUser()->getAuthUser(), $form->getValue('agreement_comments'), $msg_files);

        }

        ///return $this->sendFormBindResult($form);
        return $this->sendFormBindResult($form, 'window.accept_decline_form.onResponse');
    }

    function executeDeclineReport(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $agreement_comments = $request->getParameter('agreement_comments');

        $form = new AgreementDeclineForm(array(), array(
            'comments_file_path' => AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH
        ));
        $form->bind(
            array(
//        'decline_reason_id' => $request->getPostParameter('decline_reason_id'),
                'agreement_comments' => $request->getPostParameter('agreement_comments'),
                'designer_approve' => 0
            ),
            array()//$this->getCommentFiles($request)
        );

        if ($form->isValid()) {
            $report = $model->getReport();
            $this->forward404Unless($report);

            $report->setAgreementComments('');
            if ($agreement_comments) {
                $report->setAgreementComments($agreement_comments);
                $report->save();
            }

            if ($model->getManagerStatus() != 'wait') {
                $model->setDesignerStatus('declined');
                $model->save();
            }

            $comment = $report->getSpecialistComment($this->getUser()->getAuthUser());
            $this->forward404Unless($comment);

            $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

            $utils = new AgreementReportStatusUtils();
            $utils->declineComment(
                $comment,
                $this->getUser()->getAuthUser(),
                $form->getValue('agreement_comments'),
                $msg_files,
                'дизайнера'
            );

            $discussion = $model->getDiscussion();
            if ($discussion) {
                $discussion->activeDisabledMessages();
            }

            $this->sendMails($model);
        }

        return $this->sendFormBindResult($form, 'window.decline_report_form.onResponse');
    }

    function executeAcceptReport(sfWebRequest $request)
    {
        $model = $this->getModel($request);
        $this->forward404Unless($model);

        $action_type = $request->getParameter('action_type');
        if ($action_type == self::DECLINE_REPORT_ACTION) {
            return $this->executeDeclineReport($request);
        }

        $form = new AgreementAcceptForm(array(), array(
            'comments_file_path' => AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH
        ));

        $form->bind(
            array(
                'agreement_comments' => $request->getPostParameter('agreement_comments'),
                'designer_approve' => 0
            ),
            array()//$this->getCommentFiles($request)
        );

        if ($form->isValid()) {
            $report = $model->getReport();
            $this->forward404Unless($report);

            if ($model->getManagerStatus() == 'accepted') {
                $model->setDesignerStatus('accepted');
                $model->save();

                $report->setStatus('accepted');
                $report->save();
            }

            $comment = $report->getSpecialistComment($this->getUser()->getAuthUser());
            $this->forward404Unless($comment);

            $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

            $utils = new AgreementReportStatusUtils();
            $utils->acceptComment($comment, $this->getUser()->getAuthUser(), $form->getValue('agreement_comments'), $msg_files, 'дизайнера');

            $discussion = $model->getDiscussion();
            if ($discussion) {
                $discussion->activeDisabledMessages();
            }

            $this->sendMails($model);
        }

        return $this->sendFormBindResult($form, 'window.accept_decline_form.onResponse');
        //return $this->sendFormBindResult($form);
    }

    function outputModels(sfWebRequest $request)
    {
        $sorts = array(
            'id' => 'm.id',
            'dealer' => 'm.dealer_id', // сортировка по id дилеров (фактически - это группировка)
            'name' => 'm.name',
            'cost' => 'm.cost',
            'updated_at' => 'm.updated_at'
        );

        $sort_column = $this->getSortColumn();
        $sort_direct = $this->getSortDirection();

        $sql_sort = 'm.updated_at';
        //$sql_sort = 'm.id';
        if (isset($sorts[$sort_column]))
            $sql_sort = $sorts[$sort_column] . ' ' . ($sort_direct ? 'DESC' : 'ASC');

        $user = $this->getUser()->getAuthUser();
        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activity a')
            ->innerJoin('m.ModelType mt WITH mt.concept=?', false)
            ->leftJoin('m.Comments mc')
            ->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r')
            ->leftJoin('r.Comments rc')
            ->leftJoin('m.Dealer dealer')
            ->where('m.wait_specialist=?', true)
            ->andWhere('(mc.user_id=? and mc.status=?) or (rc.user_id=? and rc.status=?)', array($user->getId(), 'wait', $user->getId(), 'wait'));


        /*if ($user->isManager() || $user->isSpecialist()) {
            $query->leftJoin('dealer.LogEntries logs')
                ->andWhere('logs.private_user_id = ?', 0)
                ->andWhere('logs.icon = ?', 'clip')
                ->orderBy('logs.created_at ASC');

        } else {
            $query->orderBy($sql_sort);
        }*/
        $query->orderBy($sql_sort);

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
                default:
                    $query->andWhere('m.wait_specialist=?', true);
                    break;
            }

            if ($this->getDealerFilter())
                $query->andWhere('m.dealer_id=?', $this->getDealerFilter()->getId());

            if ($this->getStartDateFilter())
                $query->andWhere('m.created_at>=?', D::toDb($this->getStartDateFilter()));
            if ($this->getEndDateFilter())
                $query->andWhere('m.created_at<=?', D::toDb($this->getEndDateFilter()));
        }

        $this->models = $query->execute();

        if ($user->isManager() || $user->isSpecialist()) {
            $mods = array();

            foreach ($this->models as $m) {
                $mods[strtotime($m->getModelAcceptToDate($this->getUser()->isImporter() ? false : $this->getUser()->isDealerUser()))] = $m;
            }

            ksort($mods, SORT_NUMERIC);
            $this->models = $mods;
        }

    }

    function outputUsers()
    {
        $this->activateUser();

        $query = UserTable::getInstance()
            ->createQuery('u')
            ->innerJoin('u.DealerUsers d')
            ->innerJoin('d.Dealer du')
            ->innerJoin('u.Group g')
            ->innerJoin('g.Roles r')
            ->where('1 = 1')
            ->orderBy('u.id ASC');
        //->execute();

        if ($this->getDealerRoleFilter() && $this->getDealerRoleFilter() != -1)
            $query->andWhere('g.id = ?', $this->getDealerRoleFilter());

        if ($this->getDealerNumberFilter())
            $query->andWhere('du.number = ?', "93500" . $this->getDealerNumberFilter());

        if ($this->getDealerPostFilter())
            $query->andWhere('u.post LIKE ?', $this->getDealerPostFilter() . '%');

        $this->users = $query->execute();

    }

    function outputSearchResult(sfWebRequest $request)
    {
        $sorts = array(
            'id' => 'm.id',
            'dealer' => 'm.dealer_id', // сортировка по id дилеров (фактически - это группировка)
            'name' => 'm.name',
            'cost' => 'm.cost',
            'updated_at' => 'm.updated_at'
        );

        $sort_column = $this->getSortColumn();
        $sort_direct = $this->getSortDirection();

        $sql_sort = 'm.id';
        if (isset($sorts[$sort_column]))
            $sql_sort = $sorts[$sort_column] . ' ' . ($sort_direct ? 'DESC' : 'ASC');

        $user = $this->getUser()->getAuthUser();
        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activity a')
            ->innerJoin('m.ModelType mt WITH mt.concept=?', false)
            ->leftJoin('m.Comments mc')
            ->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r')
            ->leftJoin('r.Comments rc')
            ->leftJoin('m.Dealer dealer')
            //->where('m.wait_specialist=?', true);
            ->where('1 = 1');
        $query->orderBy($sql_sort);

        if ($this->getModelFilter() || $this->getDealerFilter() || $this->getStartDateFilter() || $this->getEndDateFilter()) {
            if ($this->getModelFilter())
                $query->andWhere('m.id=?', $this->getModelFilter());
            else {
                if ($this->getDealerFilter())
                    $query->andWhere('m.dealer_id=?', $this->getDealerFilter()->getId());

                if ($this->getStartDateFilter())
                    $query->andWhere('m.created_at>=?', D::toDb($this->getStartDateFilter()));
                if ($this->getEndDateFilter())
                    $query->andWhere('m.created_at<=?', D::toDb($this->getEndDateFilter()));
            }

            $this->models = $query->execute();
        } else
            $this->models = null;

    }

    function activateUser()
    {
        $default = $this->getUser()->getAttribute('dealer_id_to_activate', -1, self::FILTER_NAMESPACE);
        $id = $this->getRequestParameter('dealer_id_to_activate', $default);

        if ($id != -1) {
            $user = UserTable::getInstance()->find($id);

            if (!empty($user)) {
                $user->setActive(true);
                $user->save();
            }

            $this->redirect('@agreement_module_specialist_users');
        }

    }

    function outputConcepts()
    {
        $user = $this->getUser()->getAuthUser();

        $this->concepts = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activity a')
            ->innerJoin('m.ModelType mt WITH mt.concept=?', true)
            ->leftJoin('m.Comments mc')
            ->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r')
            ->leftJoin('r.Comments rc')
            ->where('m.wait_specialist=?', true)
            ->andWhere('(mc.user_id=? and mc.status=?) or (rc.user_id=? and rc.status=?)', array($user->getId(), 'wait', $user->getId(), 'wait'))
            ->orderBy('m.id desc')
            ->execute();
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

    protected function attachCommentsFileToMessage(ValidatedFile $uploaded_file, Message $message)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile($message->getId() . '-' . $uploaded_file->generateFilename());
        $uploaded_file->save(sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile());
        $file->save();
    }

    function outputFilter()
    {
        $this->outputDealers();

        $this->outputWaitFilter();
        $this->outputDealerFilter();
        $this->outputStartDateFilter();
        $this->outputEndDateFilter();
        $this->outputModelFilter();


    }

    function outputUsersFilter()
    {
        $this->outputDealerRole();
        $this->outputDealerNumber();
        $this->outputDealerPost();
    }

    function getWaitFilter()
    {
        $default = $this->getUser()->getAttribute('wait', 'specialist', self::FILTER_NAMESPACE);
        $wait = $this->getRequestParameter('wait', $default);

        $this->getUser()->setAttribute('wait', $wait, self::FILTER_NAMESPACE);

        return $wait;
    }

    function outputDealerRole()
    {
        $this->dealer_role = $this->getDealerRoleFilter();
    }

    function outputDealerNumber()
    {
        $this->dealer_number = $this->getDealerNumberFilter();
    }

    function outputDealerPost()
    {
        $this->dealer_post = $this->getDealerPostFilter();
    }

    function getDealerRoleFilter()
    {
        $default = $this->getUser()->getAttribute('dealer_role', '-1', self::FILTER_NAMESPACE);
        $role = $this->getRequestParameter('dealer_role', $default);
        $this->getUser()->setAttribute('dealer_role', $role, self::FILTER_NAMESPACE);

        return $role;
    }

    function getDealerNumberFilter()
    {
        $default = $this->getUser()->getAttribute('dealer_number', '', self::FILTER_NAMESPACE);
        $number = $this->getRequestParameter('dealer_number', $default);
        $this->getUser()->setAttribute('dealer_number', $number, self::FILTER_NAMESPACE);

        return $number;
    }

    function getDealerPostFilter()
    {
        $default = $this->getUser()->getAttribute('dealer_post', '', self::FILTER_NAMESPACE);
        $post = $this->getRequestParameter('dealer_post', $default);
        $this->getUser()->setAttribute('dealer_post', $post, self::FILTER_NAMESPACE);

        return $post;
    }

    function outputWaitFilter()
    {
        $this->wait_filter = $this->getWaitFilter();
    }

    function outputDealerFilter()
    {
        $this->dealer_filter = $this->getDealerFilter();
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

    function getSortColumn()
    {
        //return $this->getUser()->getAttribute(self::SORT_ATTR, 'id');
        return $this->getUser()->getAttribute(self::SORT_ATTR, 'updated_at');
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
            //->where('m.wait_specialist=? and m.id=? ', array(true, $request->getParameter('id')))
            ->where('m.id=? ', array($request->getParameter('id')))
            ->fetchOne();
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
            $id = $this->getRequestParameter('dealer_id', $default);

            $this->getUser()->setAttribute('dealer_id', $id, self::FILTER_NAMESPACE);

            $this->_dealer_filter = $id ? DealerTable::getInstance()->find($id) : false;
        }
        return $this->_dealer_filter;
    }

    function getStartDateFilter()
    {
        return $this->getDateFilter('start_date');
    }

    function getEndDateFilter()
    {
        return $this->getDateFilter('end_date');
    }

    function getModelFilter()
    {
        $default = $this->getUser()->getAttribute('model', '', self::FILTER_NAMESPACE);
        $model_id = $this->getRequestParameter('model', $default);
        $this->getUser()->setAttribute('model', $model_id, self::FILTER_NAMESPACE);

        return $model_id;
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

    private function setAcceptDecline($model, $status = 'accept', $from_report = true) {
        if ($status == 'accept') {
            $model->setDesignerStatus('accepted');

            if ($model->isModelScenario() && $model->getManagerStatus() == 'accepted') {
                if ($model->getStep1() == "wait" && $model->getStep2() == "none") {
                    $model->setStep1("accepted");
                } else if ($model->getStep1() == "accepted" && $model->getStep2() == "wait") {
                    $model->setStep2("accepted");
                    $model->setStatus('accepted');
                }
                $model->save();
            }

            if ($model->getManagerStatus() == 'accepted' && !$model->isModelScenario()) {
                $model->setStatus('accepted');
            } else {
                if (!$from_report) {
                    $model->setStatus('declined');
                }
            }
        } else {
            $model->setDesignerStatus('declined');
            if ($model->getManagerStatus() == 'declined') {
                $model->setStatus('declined');
            }

            if ($model->isModelScenario()) {
                if ($model->getStep1() == "accepted") {
                    $model->setStep2("none");
                } else {
                    $model->setStep1("none");
                }
            }
        }
        $model->save();

        $this->sendMails($model);
    }

    /**
     *
     * @param AgreementModel $model
     */
    private function sendMails(AgreementModel $model) {
        $mails_list = MailMessageTable::getInstance()->createQuery()->where('model_id = ? and can_send = ?', array($model->getId(), false))->execute();
        foreach ($mails_list as $mail) {
            $mail->setCanSend(true);
            $mail->save();
        }
    }
}
