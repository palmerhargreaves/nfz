<?php

require_once dirname(__FILE__) . '/../lib/userGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/userGeneratorHelper.class.php';

/**
 * user actions.
 *
 * @package    Servicepool2.0
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userActions extends autoUserActions
{
    protected $action;
    protected $activated = false;

    public function preExecute()
    {
        $this->dispatcher->connect('admin.save_object', array($this, 'onSaveObject'));
        $this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));

        parent::preExecute();
    }

    public function executeCreate(sfWebRequest $request)
    {
        $this->action = 'add';

        parent::executeCreate($request);
    }

    public function executeUpdate(sfWebRequest $request)
    {
        $this->action = 'edit';

        parent::executeUpdate($request);
    }

    protected function processForm(sfWebRequest $request, sfForm $form)
    {
        if ($form->getObject()->isNew()) {
            $form->getValidator('new_password')->setOption('required', true);
        } else {
            $this->activated = $form->getObject()->getActive();
        }

        return parent::processForm($request, $form);
    }

    protected function addToLog($action, $object)
    {
        $description = '';
        if ($action == 'add')
            $description = 'Добавлен пользователь "' . $object->getEmail() . '"';
        elseif ($action == 'edit') {
            if ($object->getActive() && !$this->activated)
                $this->sendActivationEmail($object);

            $description = 'Изменён пользователь "' . $object->getEmail() . '"';
        } elseif ($action == 'delete')
            $description = 'Удалён пользователь "' . $object->getEmail() . '"';

        LogEntryTable::getInstance()->addEntry(
            $this->getUser()->getAuthUser(),
            'user',
            $action,
            'Пользователи',
            $description,
            '',
            null,
            $object->getId()
        );
    }

    protected function buildQuery()
    {
        $query = parent::buildQuery();

        if (!$this->getUser()->hasCredential('admin'))
            $query->innerJoin('r.Group g')->innerJoin('g.Roles r WITH r.role=?', 'manager');

        return $query;
    }

    public function onSaveObject(sfEvent $event)
    {
        $object = $event['object'];
        $this->addToLog($this->action, $object);

        if ($this->form->getValue('new_password')) {
            AuthFactory::getInstance()->getAuthenticator()->setupPassword($object, $this->form->getValue('new_password'));
            $object->save();
        }

        if ($this->getUser()->hasCredential('importer') && !$this->getUser()->hasCredential('admin')) {
            $object->setGroup(UserGroupTable::getInstance()->getManagerGroup());
            $object->save();
        }

        $natural_person_id = $this->form->getValue('natural_person_id');
        if (!is_null($natural_person_id) && $object) {
            NaturalPersonTable::getInstance()->createQuery()->update('NaturalPerson np')->set('np.regional_manager_nfz_id', 0)->where('regional_manager_nfz_id = ?', $object->getId())->execute();

            $natural_person = NaturalPersonTable::getInstance()->find($natural_person_id);
            if ($natural_person) {
                $natural_person->setRegionalManagerNfzId($object->getId());
                $natural_person->save();
            }
        } else if ($object) {
            $natural_person = NaturalPersonTable::getInstance()->createQuery()->where('regional_manager_nfz_id = ?', $object->getId())->fetchOne();
            if ($natural_person) {
                $natural_person->setRegionalManagerNfzId(0);
                $natural_person->save();
            }
        }
    }

    public function onDeleteObject(sfEvent $event)
    {
        $this->addToLog('delete', $event['object']);
    }

    protected function sendActivationEmail(User $user, $password)
    {
        $message = new UserActivationMail($user);
        $message->setPriority(1);
        sfContext::getInstance()->getMailer()->send($message);
    }

    public function executeOnLoadUserBindedDealers(sfWebRequest $request)
    {
        $this->loadUserBindDealersData($request);
    }

    public function executeOnUnbindUserDealer(sfWebRequest $request)
    {
        $user_id = $request->getParameter('user_id');
        $dealer_id = $request->getParameter('dealer_id');

        DealerUserTable::getInstance()->createQuery()->delete()->where('user_id = ? and dealer_id = ?', array($user_id, $dealer_id))->execute();

        $this->loadUserBindDealersData($request);

        $this->setTemplate('onLoadUserBindedDealers');
    }

    public function executeOnBindUserDealer(sfWebRequest $request)
    {
        $user_id = $request->getParameter('user_id');
        $dealer_id = $request->getParameter('dealer_id');

        $user_bind_dealer = new DealerUser();
        $user_bind_dealer->setArray(
            array(
                'user_id' => $user_id,
                'dealer_id' => $dealer_id
            )
        );
        $user_bind_dealer->save();

        $this->loadUserBindDealersData($request);

        $this->setTemplate('onLoadUserBindedDealers');
    }

    public function executeOnUserBindedDealersReloadRow(sfWebRequest $request)
    {
        $this->user = UserTable::getInstance()->find($request->getParameter('user_id'));
    }

    private function loadUserBindDealersData(sfWebRequest $request) {
        $this->binded_dealers = DealerUserTable::getInstance()->createQuery('du')
            ->leftJoin('du.Dealer d')
            ->where('user_id = ?', $request->getParameter('user_id'))
            ->orderBy('d.number ASC')
            ->execute();

        $already_binded_dealers = array();
        foreach ($this->binded_dealers as $bind_dealer) {
            $already_binded_dealers[] = $bind_dealer->getDealerId();
        }

        $this->dealers_list = DealerTable::getInstance()->createQuery()
            ->where('dealer_type = ? or dealer_type = ?', array(Dealer::TYPE_NFZ, Dealer::TYPE_NFZ_PKW))
            ->andWhereNotIn('id', $already_binded_dealers)
            ->andWhere('number LIKE ?', '93500%')
            ->orderBy('number ASC')->execute();
    }
}
