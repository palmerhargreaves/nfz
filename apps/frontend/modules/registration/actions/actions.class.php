<?php

/**
 * registration actions.
 *
 * @package    Servicepool2.0
 * @subpackage registration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class registrationActions extends ActionsWithJsonForm
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request)
    {
        $form = new RegistrationForm();

        if ($request->getParameter('company_type') == 'dealer')
            $form->getValidator('dealer_id')->setOption('required', true);
        elseif ($request->getParameter('company_type') == 'other')
            $form->getValidator('company_name')->setOption('required', true);

        $form->bind(array(
            'email' => $request->getPostParameter('email'),
            'password' => $request->getPostParameter('password'),
            'fio' => $request->getPostParameter('fio'),
            'dealer_id' => $request->getPostParameter('dealer_id'),
            'company_name' => $request->getPostParameter('company_name'),
            'company_type' => $request->getPostParameter('company_type'),
            'post' => $request->getPostParameter('post'),
            'phone' => $request->getPostParameter('phone'),
            'mobile' => $request->getPostParameter('mobile'),
            'agree' => $request->getPostParameter('agree'),
        ));

        if ($form->isValid()) {
            $user = new User();
            $user->setArray(array(
                'email' => $form->getValue('email'),
                'name' => $form->getUserName(),
                'surname' => $form->getUserSurname(),
                'patronymic' => $form->getUserPatronymic(),
                'company_type' => $form->getValue('company_type'),
                'company_name' => $form->getValue('company_name'),
                'active' => false,
                'post' => $form->getValue('post'),
                'phone' => $form->getValue('phone'),
                'mobile' => $form->getValue('mobile')
            ));
            $this->setUserGroupByCompanyType($user);
            AuthFactory::getInstance()->getAuthenticator()->setupPassword($user, $form->getValue('password'));
            $user->save();

            $this->processCompanyType($form, $user);
        }

        return $this->sendFormBindResult($form);
    }

    function executeAgreement(sfWebRequest $request)
    {

    }

    function executeCompanyDep(sfWebRequest $request)
    {
        $this->companyDep = $request->getParameter('companyDep');
    }

    function executeSp1(sfWebRequest $request)
    {
        $this->forward404Unless(sfContext::getInstance()->get('register_user'), 'registration is not available');

        $login = $request->getParameter('login');
        $this->forward404Unless($login, 'login is not found');

        $user = UserTable::getInstance()->findOneByEmail($login);
        if ($user)
            return sfView::ERROR;

        $dealer_number = '93500' . $request->getParameter('dealer_number');
        $dealer = DealerTable::getInstance()->findOneByNumber($dealer_number);

        $this->forward404Unless($dealer, 'dealer is not found');

        $user = new User();
        $user->setArray(array(
            'email' => $login,
            'name' => $request->getParameter('name'),
            'surname' => $request->getParameter('family'),
            'company_type' => 'dealer',
            'company_name' => $request->getParameter('company'),
            'active' => false,
            'post' => $request->getParameter('post'),
            'phone' => $request->getParameter('phone'),
            'mobile' => $request->getParameter('mobile_phone')
        ));
        $this->setUserGroupByCompanyType($user);
        AuthFactory::getInstance()->getAuthenticator()->setupPassword($user, $request->getParameter('password'));
        $user->save();

        $dealer_user = new DealerUser();
        $dealer_user->setUser($user);
        $dealer_user->setDealer($dealer);
        $dealer_user->setApproved(false);
        $dealer_user->save();

    }

    protected function processCompanyType(RegistrationForm $form, User $user)
    {
        switch ($user->getCompanyType()) {
            case 'dealer':
                $this->processDealer($form, $user);
                break;
            case 'importer':
                $this->processImporterCompany($user);
                break;
            case 'other':
                $this->processOtherCompany($user);
                break;
        }
    }

    protected function processDealer(RegistrationForm $form, User $user)
    {
        $dealer_user = new DealerUser();
        $dealer_user->setUser($user);
        $dealer_user->setDealerId($form->getValue('dealer_id'));
        $dealer_user->setApproved(false);
        $dealer_user->save();

        $user->generateActivationKey();
        $user->save();

        $message = new DealerUserRegisteredForUserMail($user, $form->getValue('password'));
        sfContext::getInstance()->getMailer()->send($message);

        UserTable::getInstance()->callWithImporter(function (User $importer) use ($user) {
            if ($importer->getRegistrationNotification()) {
                $message = new DealerUserRegisteredForImporterMail($user, $importer);
                sfContext::getInstance()->getMailer()->send($message);
            }
        });

        LogEntryTable::getInstance()->addEntry(
            $user,
            'user',
            'register',
            'Пользователи',
            'Зарегистрировался пользователь "' . $user->getEmail() . '"',
            '',
            $dealer_user->getDealer()
        );
    }

    protected function processImporterCompany(User $user)
    {
        $message = new ImporterUserRegisteredForUserMail($user);
        sfContext::getInstance()->getMailer()->send($message);

        UserTable::getInstance()->callWithAdministrator(function (User $admin) use ($user) {
            if ($admin->getRegistrationNotification()) {
                $message = new ImporterRegisteredForAdminMail($user, $admin);
                sfContext::getInstance()->getMailer()->send($message);
            }
        });
    }

    protected function processOtherCompany(User $user)
    {
        $message = new OtherUserRegisteredForUserMail($user);
        sfContext::getInstance()->getMailer()->send($message);

        UserTable::getInstance()->callWithAdministrator(function (User $admin) use ($user) {
            if ($admin->getRegistrationNotification()) {
                $message = new OtherRegisteredForAdminMail($user, $admin);
                sfContext::getInstance()->getMailer()->send($message);
            }
        });
    }

    protected function setUserGroupByCompanyType(User $user)
    {
        switch ($user->getCompanyType()) {
            case 'dealer':
                $user->setGroup(UserGroupTable::getInstance()->getDealerGroup());
                break;
            case 'importer':
                $user->setGroup(UserGroupTable::getInstance()->getImporterGroup());
                break;
            case 'regional_manager':
                $user->setGroup(UserGroupTable::getInstance()->getRegionalManagerGroup());
                break;
            case 'other':
                $user->setGroup(UserGroupTable::getInstance()->getDealerGroup());
                break;
        }
    }
}
