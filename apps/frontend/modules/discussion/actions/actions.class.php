<?php

/**
 * discussion actions.
 *
 * @package    Servicepool2.0
 * @subpackage discussion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class discussionActions extends ActionsWithJsonForm
{
    /**
     * Executes state action
     *
     * @param sfRequest $request A request object
     */
    const FILTER_NAMESPACE = 'messages';

    public function executeState(sfWebRequest $request)
    {
        $discussion = $this->getDiscussion($request);
        $discussion->updateOnline($this->getUser()->getAuthUser());

        $start_message = $request->getParameter('start', false);
        if ($start_message)
            $this->messages = $discussion->getLastMessagesFrom($start_message, $this->getUser()->getAuthUser(), true);
        else
            $this->messages = $discussion->getLastMessages(10, $this->getUser()->getAuthUser());

        $this->outputFiles($this->messages);
        $this->setTemplate('messages');
        $this->setLayout(false);
    }

    public function executeNewMessages(sfWebRequest $request)
    {
        $discussion = $this->getDiscussion($request);
        $discussion->updateOnline($this->getUser()->getAuthUser());

        $this->messages = $discussion->getUnreadMessages($this->getUser()->getAuthUser());

        $this->outputFiles($this->messages);
        $this->setTemplate('messages');
        $this->setLayout(false);
    }

    public function executePrevious(sfWebRequest $request)
    {
        $this->messages = $this->getDiscussion($request)->getPreviousMessages(10, $request->getParameter('before'), $this->getUser()->getAuthUser());
        $this->outputFiles($this->messages);
        $this->setTemplate('messages');
        $this->setLayout(false);
    }

    public function executeSearch(sfWebRequest $request)
    {
        $this->messages = $this->getDiscussion($request)->searchMessages($request->getParameter('text'), $this->getUser()->getAuthUser());
        $this->outputFiles($this->messages);
        $this->setTemplate('messages');
        $this->setLayout(false);
    }

    public function executePost(sfWebRequest $request)
    {
        $text = trim(strip_tags($request->getPostParameter('message')));
        /*if (!$text)
            return sfView::NONE;*/

        $files = $request->getParameter('files');
        var_dump($files);
        exit;

        if ((!$files || !is_array($files)) && empty($text)) {
            return sfView::NONE;
        }

        $discussion = $this->getDiscussion($request);
        $user = $this->getUser()->getAuthUser();

        $message = new Message();
        $message->setDiscussion($discussion);
        $message->setUser($user);
        $message->setUserName($user->selectName());
        $message->setText($text);
        $message->save();

        $this->saveFiles($message, $request);

        $logEntry = LogEntryTable::getInstance()->createQuery()->where('message_id = ?', $message->getId())->execute();
        if($user->getAllowReceiveMails()) {
            new DealerDiscussionMail($user, $logEntry, sfConfig::get('app_mail_sender'));
        }

        return $this->sendJson(array('success' => true, 'message_data' => ''));
    }

    public function executeDealerDiscussion(sfWebRequest $request)
    {
        $dealer = DealerTable::getInstance()->find($request->getParameter('id'));
        $this->forward404Unless($dealer);

        $discussion = DealerDiscussionTable::getInstance()->findDiscussion($dealer);

        $this->getResponse()->setContentType('application/json');
        $this->getResponse()->setContent(json_encode(array('id' => $discussion->getId())));

        return sfView::NONE;
    }

    public function executeCheckForOnline(sfWebRequest $request)
    {
        $online = array();
        foreach ($this->getDiscussion($request)->getOnlineUsersInOnlinePeriod() as $user)
            $online[$user->getId()] = true;

        $this->getResponse()->setContentType('application/json');
        $this->getResponse()->setContent(json_encode($online));

        return sfView::NONE;
    }

    protected function outputFiles($messages)
    {
        $ids = array();
        foreach ($messages as $message)
            $ids[] = $message->getId();

        if (!$ids)
            return array();

        $files = MessageFileTable::getInstance()
            ->createQuery()
            ->select('*')
            ->whereIn('message_id', $ids)
            ->execute();

        $grouped_files = array();
        foreach ($files as $file) {
            $message_id = $file->getMessageId();
            if (!isset($grouped_files[$message_id]))
                $grouped_files[$message_id] = array();

            $grouped_files[$message_id][] = $file;
        }

        $this->files = $grouped_files;
    }

    protected function saveFiles(Message $message, sfWebRequest $request)
    {
        $files = $request->getParameter('files');
        if (!$files || !is_array($files))
            return;

        foreach ($files as $temp_id) {
            $temp_file = TempFileTable::getInstance()
                ->createQuery()
                ->where('user_id=? and id=?', array($this->getUser()->getAuthUser()->getId(), $temp_id))
                ->fetchOne();

            if (!$temp_file)
                continue;

            $file = new MessageFile();
            $file->setMessageId($message->getId());
            $file->applyTemp($temp_file);
            $file->save();
        }
    }

    private function getModelCommentFiles(sfWebRequest $request)
    {
        $files = $request->getFiles();
        if (!is_array($files)) {
            return $files;
        }

        $uploaded_files = Utils::getUploadedFilesByField($files, 'comments_files');
        if (!empty($uploaded_files)) {
            return $uploaded_files;
        }

        return array('comments_files' => $files['comments_files'][0]);
    }

    /**
     * Returns discussion
     *
     * @param sfWebRequest $request
     * @return Discussion
     */
    protected function getDiscussion(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $discussion = DiscussionTable::getInstance()->find($id);

        if (!$discussion) {
            $model = AgreementModelTable::getInstance()->find($id);

            if (!$model) {
                $this->forward404('обсуждение не найдено');
            }

            $discussion = $model->getDiscussion();
        }

        return $discussion;
    }

    public function executeAllMessages()
    {
        $query = MessageTable::getInstance()
            ->createQuery('m')
            ->select('*')
            ->leftJoin('m.PrivateUser u')
            ->leftJoin('u.Group gr')
            //->where('m.created_at LIKE ?', '%'.date('Y-m').'%')
            ->andWhere('m.user_id != ?', $this->getUser()->getAuthUser()->getId())
            ->andWhere('m.system != 1')
            ->andWhere('m.msg_show = ?', 1)
            ->orderBy('m.id DESC');
        //->limit(100)
        //->execute();
        $this->initPager($query);
        $this->initPaginatorData(null, 'discussion_all_messages');
    }

    public function executeMessagesList(sfWebRequest $request)
    {
        $this->tab = $request->getParameter('tab');
        if (empty($this->tab)) {
            $this->tab = Discussion::PAGER_NEW_MESSAGES;
            $this->page_parent = Discussion::PAGER_NEW_MESSAGES;
        }
        else {
            $this->page_parent = $this->tab;
        }

        $mark_as_read = $request->getParameter('mark_as_read');

        if ($this->tab == Discussion::PAGER_NEW_MESSAGES) {
            $query = DealerDiscussionTable::getInstance()->getUnreadMessages($this->getUser()->getAuthUser(), isset($mark_as_read) && $mark_as_read == 1 ? true : false);
        } else if ($this->tab == Discussion::PAGER_READED_MESSAGES) {
            $query = DealerDiscussionTable::getInstance()->getReadMessages($this->getUser()->getAuthUser(), false);
        }

        $this->initPager($query, $this->tab);
        $this->initPaginatorData(null, 'discussion_messages', $this->tab);

        if ($request->isXmlHttpRequest()) {
            $this->setTemplate('messagesListWithItems');
        }
    }

    public function executeSpecialMessagesList(sfWebRequest $request)
    {
        $discussion = $this->getDiscussion($request);
        $discussion->updateOnline($this->getUser()->getAuthUser());

        $this->messages = $discussion->getLastMessagesForDiscussion(0, $request->getParameter('message_id'), $request->getParameter('mark_as_read'), $this->getUser()->getAuthUser());

        $this->setTemplate('messages');
        $this->setLayout(false);
    }

    public function executeSpecialMessageAdd(sfWebRequest $request)
    {
        $user = UserTable::getInstance()->createQuery()->select('*')->where('id = ?', $request->getParameter('userId'))->fetchOne();
        if (!$user)
            $this->messages = '';
        else {
            $discussion = $this->getDiscussion($request);
            $discussion->updateOnline($user);

            $userMsg = $discussion->getLastMessageUser();
            if (!empty($userMsg) && $userMsg->getUser()->getId() != $user->getId()) {
                if($userMsg->getUser()->getAllowReceiveMails()) {
                    $message = new UserDiscussionInform($userMsg->getUser(), $userMsg);
                    $message->setPriority(1);
                    sfContext::getInstance()->getMailer()->send($message);
                }
            }

            $lastMsg = $discussion->addNewMessage($request, $user);
            if ($lastMsg)
                $this->saveFiles($lastMsg, $request);

            $this->messages = $discussion->getLastMessagesForDiscussion();
        }

        $this->setTemplate('messages');
        $this->setLayout(false);
    }

    public function executeSwitchToDealer(sfWebRequest $request)
    {
        if ($this->getUser()->isManager() || $this->getUser()->isImporter()) {
            $dealer = DealerTable::getInstance()->find($request->getParameter('dealer'));
            $this->forward404Unless($dealer);

            $dealer_user = DealerUserTable::getInstance()->findOneByUserId($this->getUser()->getAuthUser()->getId());

            if (!$dealer_user) {
                $dealer_user = new DealerUser();
                $dealer_user->setUser($this->getUser()->getAuthUser());
                $dealer_user->setManager(true);
            }

            $dealer_user->setDealer($dealer);
            $dealer_user->save();
        }

        $activityId = $request->getParameter('activityId');
        $modelId = $request->getParameter('modelId');

        $model = AgreementModelTable::getInstance()->find($modelId);
        $model_quarter = D::getQuarter(D::calcQuarterData($model->getCreatedAt()));
        $currentQ = D::getQuarter(D::calcQuarterData(time()));

        if ($model->isModelCompleted()) {
            $model_quarter = D::getQuarter(Utils::getModelDateFromLogEntryWithYear($modelId));
        }

        //'/activity/' + $(this).data('activity-id') + '/module/agreement/models/model/' + $(this).data('model'), '_blank');
        $this->redirect("/activity/{$activityId}/module/agreement/models/model/{$modelId}/q/".$model_quarter);
    }

    private function initPager($query, $pager_var = null)
    {
        $request = $this->getRequest();
        $page = $request->getParameter('page', 1);

        $this->tab = $request->getParameter('tab');
        if (is_null($this->tab)) {
            $this->tab = Discussion::PAGER_NEW_MESSAGES;
        }

        if ($page) {
            $max_items_on_page = sfConfig::get('app_max_items_on_page');
        } else {
            $max_items_on_page = 0;
            $page = 1;
        }

        if (is_null($pager_var)) {
            $pager_var = 'pager';
        }

        $this->$pager_var = new sfDoctrinePager(
            'Message',
            $max_items_on_page
        );

        $this->$pager_var->setQuery($query);
        $this->$pager_var->setPage($page);
        $this->$pager_var->init();

        if ($this->$pager_var->getLastPage() < $page) $this->$pager_var->setPage($this->$pager_var->getLastPage());
        $this->$pager_var->init();
    }

    private function initPaginatorData($route_object, $route_name, $pager_var = null)
    {
        if (is_null($pager_var)) {
            $pager_var = 'pager';
        }

        $request = $this->getRequest();
        $this->parameters = $request->getGetParameters();
        $this->pageLinkArray = array_merge($this->parameters, array('sf_subject' => $route_object, 'tab' => $pager_var));

        $paginatorData = $pager_var.'_paginatorData';
        $this->$paginatorData = array('pager' => $this->$pager_var,
            'pageLinkArray' => $this->pageLinkArray,
            'route' => $route_name);
    }

    public function executeDownloadFile(sfWebRequest $request)
    {
        $id = $request->getParameter('file');

        $msgFile = MessageFileTable::getInstance()->createQuery()->where('id = ?', $id)->fetchOne();
        if ($msgFile) {
            $filePath = sfConfig::get('app_uploads_path') . '/' . MessageFile::FILE_PATH . '/' . $msgFile->getFileName();

            $file_download_result = F::downloadFile($filePath, $msgFile->getFile());
            if (empty($file_download_result)) {
                $this->getResponse()->setContentType('application/json');
                $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
            } else {
                $file_download_result != 'success' ? $this->redirect($file_download_result) : '';
            }
        }

        return sfView::NONE;
    }

    public function executeGetMessagesListByType(sfWebRequest $request) {
        $this->pager = null;
        $this->paginatorData = null;

        $this->page_parent = $request->getParameter('type_parent');
        $this->message_type = $request->getParameter('type');
        $this->start_from = $request->getParameter('start_from');

        $this->messages = Discussion::getMessagesListByParentAndType($this->getUser()->getAuthUser(), $this->page_parent, $this->message_type, $this->start_from);
    }
}
