<?php

/**
 * main actions.
 *
 * @package    Servicepool2.0
 * @subpackage home
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mainActions extends sfActions
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */

    function executeIndex(sfWebRequest $request)
    {
        $user = $this->getUser()->getAuthUser();
        $dealer = $user->getDealer();

        /*if ($dealer && $dealer->isNFZ() && (!$user->isAdmin() && !$user->isManager() && !$user->isImporter() && !$user->isSpecialist())) {
            $this->redirect('home/index');
        }*/
    }
}
