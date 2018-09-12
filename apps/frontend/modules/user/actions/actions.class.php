<?php

/**
 * user actions.
 *
 * @package    Servicepool2.0
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userActions extends ActionsWithJsonForm
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    function executeIndex(sfWebRequest $request)
    {
        $this->forward('default', 'module');
    }

    function executeChangePassword(sfWebRequest $request)
    {
        $form = new ChangePasswordForm();

        $form->bind($request->getPostParameters());
        if ($form->isValid()) {
            $user = $this->getUser()->getAuthUser();
            AuthFactory::getInstance()->getAuthenticator()->setupPassword($user, $form->getValue('new_password'));
            $user->save();
        }

        return $this->sendFormBindResult($form);
    }

    function executeSwitchToDealer(sfWebRequest $request)
    {
        if ($this->getUser()->isManager() || $this->getUser()->isImporter() || $this->getUser()->isDealerUser() || $this->getUser()->isRegionalManager()) {
            $dealer = DealerTable::getInstance()->find($request->getParameter('dealer_id'));
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

        $this->redirect('@homepage');
    }

    function executeDetachFromDealer(sfWebRequest $request)
    {
        if ($this->getUser()->isManager() || $this->getUser()->isImporter() || $this->getUser()->isDealerUser() || $this->getUser()->isRegionalManager())   {
            $dealer_user = DealerUserTable::getInstance()->findOneByUserId($this->getUser()->getAuthUser()->getId());
            if ($dealer_user)
                $dealer_user->delete();
        }

        $this->redirect('@homepage');
    }
}
