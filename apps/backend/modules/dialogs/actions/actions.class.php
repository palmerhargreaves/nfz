<?php

require_once dirname(__FILE__) . '/../lib/dialogsGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/dialogsGeneratorHelper.class.php';

/**
 * dialogs actions.
 *
 * @package    Servicepool2.0
 * @subpackage dialogs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dialogsActions extends autoDialogsActions
{
    public function executeGetDialogsUsersLimitsData(sfWebRequest $request) {
        $this->dialogId = $request->getParameter('id');

        $this->usersPosts = UserGroupTable::getInstance()
            ->createQuery()
            ->execute();

        $this->users = UserTable::getInstance()
            ->createQuery()
            ->where('active = ?', true)
            ->orderBy('id ASC')
            ->execute();

        $postLimits = array();
        $result = DialogsUsersLimitsTable::getInstance()
            ->createQuery()
            ->where('dialog_id = ?', $this->dialogId)
            ->andWhere('post_type != ?', 0)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach($result as $res) {
            $postLimits[] = $res['post_type'];
        }

        $this->postLimits = $postLimits;

        $usersLimits = array();
        $result = DialogsUsersLimitsTable::getInstance()
            ->createQuery()
            ->where('dialog_id = ?', $this->dialogId)
            ->andWhere('user_id != ?', 0)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach($result as $res) {
            $usersLimits[] = $res['user_id'];
        }

        $this->usersLimits = $usersLimits;
    }

    public function executeAddLimitData(sfWebRequest $request) {
        $dialogId = $request->getParameter('dialogId');
        $usersPosts = $request->getParameter('usersPosts');
        $users = $request->getParameter('users');

        DialogsUsersLimitsTable::getInstance()
            ->createQuery()
            ->where('dialog_id = ?', $dialogId)
            ->andWhere('post_type != ?', 0)
            ->delete()
            ->execute();

        if(!empty($usersPosts)) {
            foreach ($usersPosts as $post) {
                $item = new DialogsUsersLimits();
                $item->setArray(array(
                    'dialog_id' => $dialogId,
                    'post_type' => $post
                ));
                $item->save();
            }
        }

        DialogsUsersLimitsTable::getInstance()
            ->createQuery()
            ->where('dialog_id = ?', $dialogId)
            ->andWhere('user_id != ?', 0)
            ->delete()
            ->execute();

        if(!empty($users)) {
            foreach($users as $user) {
                $item = new DialogsUsersLimits();
                $item->setArray(array(
                    'dialog_id' => $dialogId,
                    'user_id' => $user
                ));
                $item->save();
            }
        }

        if(!empty($usersPosts) || !empty($users)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false));
        }

        return sfView::NONE;
    }
}
