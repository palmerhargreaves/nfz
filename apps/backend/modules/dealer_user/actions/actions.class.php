<?php

require_once dirname(__FILE__).'/../lib/dealer_userGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dealer_userGeneratorHelper.class.php';

/**
 * dealer_user actions.
 *
 * @package    Servicepool2.0
 * @subpackage dealer_user
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dealer_userActions extends sfActions
{
    public function executeIndex() {
        $this->usersWithDealers = DealerUserTable::getInstance()
            ->createQuery()
            ->groupBy('dealer_id ASC')
            ->orderBy('user_id ASC')
                ->execute();
    }

    public function executeChangeApproveStatus(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $item = DealerUserTable::getInstance()->find($id);
        if($item) {
            $appoved = $item->getApproved();

            $item->setApproved(!$appoved);
            $item->save();

            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Error'));
        }

        return sfView::NONE;
    }

    public function executeChangeManagerStatus(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $item = DealerUserTable::getInstance()->find($id);
        if($item) {
            $manager = $item->getManager();

            $item->setManager(!$manager);
            $item->save();

            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Error'));
        }

        return sfView::NONE;
    }

    public function executeDeleteDealerUser(sfWebRequest $request) {
        $id = $request->getParameter('id');

        $item = DealerUserTable::getInstance()->find($id);
        if($item) {
            $item->delete();

            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Error'));
        }

        return sfView::NONE;
    }

    public function executeDealerLoadUsers(sfWebRequest $request)
    {
        $dealerUsers = DealerUserTable::getInstance()
            ->createQuery()
            ->select('user_id')
            ->where('dealer_id = ?', $request->getParameter('id'))
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $users = array();
        foreach($dealerUsers as $d) {
            $users[] = $d['user_id'];
        }

        $this->users = UserTable::getInstance()
            ->createQuery()
            ->select()
            ->whereNotIn('id', $users)
            ->andWhere('active = ?', true)
            ->orderBy('id ASC')
            ->execute();
    }

    public function executeDealerUserAdd(sfWebRequest $request) {
        $item = new DealerUser();

        $item->setDealerId($request->getParameter('dealerId'));
        $item->setUserId($request->getParameter('userId'));
        $item->setManager(true);
        $item->save();

        return sfView::NONE;
    }
}
