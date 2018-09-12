<?php

class userComponents extends sfComponents
{
    function executeUser()
    {
        $user = $this->getUser()->getAuthUser();
        $this->user_name = $user->getName() || $user->getSurname()
            ? $user->getName() . ' ' . $user->getSurname()
            : $user->getEmail();

        $this->dealer = $this->getUser()->getAuthUser()->getDealer()
            ? $this->getUser()->getAuthUser()->getDealer()
            : null;

        $this->group = $user->getGroup();
    }
}