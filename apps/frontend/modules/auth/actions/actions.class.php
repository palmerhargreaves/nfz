<?php

/**
 * auth actions.
 *
 * @package    Servicepool2.0
 * @subpackage auth
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class authActions extends ActionsWithJsonForm
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    function executeIndex(sfWebRequest $request)
    {
        $path_info = $request->getPathInfoArray();
        $this->getUser()->setAttribute('request_uri', $path_info['REQUEST_URI']);
    }

    function executeAuth(sfWebRequest $request)
    {
        $form = new AuthForm();
        $form->bind($request->getPostParameters());

        if ($form->isValid()) {
            $user = UserTable::getInstance()->findOneByEmail($form->getValue('login'));

            $dealer = $user->getDealer();
            if ($dealer && (!$dealer->isNFZ() && !$dealer->isNFZ_PKW()) && (!$user->isAdmin() && !$user->isManager() && !$user->isImporter() && !$user->isSpecialist())) {
                return $this->sendJson(
                    array
                    (
                        'success' => false,
                        'errors' => 'Error'
                    )
                );
            }

            $this->getUser()->login($user, $form->getValue('remember'));

        } elseif ($this->registrationFromSp1($request)) {
            return $this->executeAuth($request);
        }

        return $this->sendFormBindResult($form);
    }

    function executeRedirect()
    {
        if ($this->getUser()->hasAttribute('request_uri')) {
            $path = trim($this->getUser()->getAttribute('request_uri'), '/');
            $this->getUser()->getAttributeHolder()->remove('request_uri');

            $path = "main";
            $this->redirect(
                ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . $path
            );
        } else {
            $this->redirect('home/index');
        }
    }

    function executeLogout(sfWebRequest $request)
    {
        $this->getUser()->logout();

        $this->redirect('home/index');
    }

    protected function registrationFromSp1(sfWebRequest $request)
    {
        $email = $request->getPostParameter('login');
        $password = $request->getPostParameter('password');
        if (!$password || !$email)
            return false;

        if (UserTable::getInstance()->findOneByEmail($email))
            return false;

        $sp1_user = Sp1UserTable::getInstance()->findOneByEmail($email);
        if (!$sp1_user)
            return false;

        $dealer_number = '93500' . $sp1_user->getDealerNumber();
        $dealer = DealerTable::getInstance()->findOneByNumber($dealer_number);

        if (!$dealer)
            return false;

        $user = new User();
        $user->setArray(array(
            'email' => $email,
            'name' => $sp1_user->getName(),
            'surname' => $sp1_user->getFamily(),
            'company_type' => 'dealer',
            'company_name' => $sp1_user->getCompany(),
            'active' => true,
            'post' => $sp1_user->getPost(),
            'phone' => $sp1_user->getPhone(),
            'mobile' => $sp1_user->getMobilePhone()
        ));
        $user->setGroup(UserGroupTable::getInstance()->getDealerGroup());
        AuthFactory::getInstance()->getAuthenticator()->setupPassword($user, $password);
        $user->save();

        $dealer_user = new DealerUser();
        $dealer_user->setUser($user);
        $dealer_user->setDealer($dealer);
        $dealer_user->setApproved(false);
        $dealer_user->save();

        Doctrine_Manager::connection()->flush();

        return true;
    }
}
