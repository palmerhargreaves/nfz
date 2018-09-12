<?php

/**
 * BasicAuthUser
 *
 * @author Сергей
 */
class BasicAuthUser extends sfBasicSecurityUser
{
    const REMEMBER_COOKIE = 'user_remember';

    protected $user = null;

    function login(User $user, $remember = false)
    {
        $this->logout();

        $this->setAuthenticated(true);
        $this->setAttribute('user_id', $user->getId());

        $group = $user->getGroup();
        foreach ($group->getRoles() as $role)
            $this->addCredential($role->getRole());

        $this->remember(!$remember);
    }

    function logout()
    {
        if ($this->isAuthenticated()) {
            $this->setAuthenticated(false);
            $this->getAttributeHolder()->remove('user_id');
            $this->clearCredentials();
            $this->forget();
            $this->user = null;
        }
    }

    /**
     * Returns an authenticated user
     *
     * @return User
     */
    function getAuthUser()
    {
        if ($this->hasAttribute('user_id')) {
            if (!$this->user) {
                $this->user = UserTable::getInstance()->find($this->getAttribute('user_id'));
            }
            if ($this->user)
                return $this->user;
        }

        $this->logout();

        throw new UserIsNotAuthenticatedException();
    }

    function isDealerUser()
    {
        return $this->getAuthUser()->isDealerUser();
    }

    function isAdmin()
    {
        return $this->getAuthUser()->isAdmin();
    }

    function isManager()
    {
        return $this->getAuthUser()->isManager();
    }

    function isImporter()
    {
        return $this->getAuthUser()->isImporter();
    }

    function isSpecialist()
    {
        return $this->getAuthUser()->isSpecialist();
    }

    protected function remember($session_only)
    {
        setcookie(self::REMEMBER_COOKIE, '1', $session_only ? 0 : time() + $this->options['timeout'], '/');
    }

    protected function forget()
    {
        setcookie(self::REMEMBER_COOKIE, '', time() - 3600, '/');
    }

    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
    {
        parent::initialize($dispatcher, $storage, $options);

        if (!$this->isAuthenticated())
            return;

        if (!isset($_COOKIE[self::REMEMBER_COOKIE]))
            $this->logout();
    }

    function isRegionalManager() {
        return $this->getAuthUser()->isRegionalManager();
    }
}
