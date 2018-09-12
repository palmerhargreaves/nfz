<?php
/**
 * Created by PhpStorm.
 * User: averinbox
 * Date: 26.01.16
 * Time: 15:04
 */

class dealer_listActions extends sfActions {

    public function executeIndex(sfWebRequest $request)
    {
        $this->city_id = $request->getGetParameter('city');
        $this->search_num = $request->getGetParameter('search_num');

        $order = $request->getGetParameter('order');
        if(empty($order))
            $order = 'name';

        $this->direction = $request->getGetParameter('direction');
        if (empty($this->direction))
            $this->direction = 'DESC';

        $Dealer = DealerTable::getInstance()->createQuery()->select()->where('number LIKE ?', '%93500%')->andWhere('importer_id = 1')->orderBy('number');

        $q = Doctrine_Query::create()
            ->from('Dealer d')
            ->leftJoin('d.City as c')
            ->leftJoin('d.RegionalManager as rm')
            ->leftJoin('d.NfzRegionalManager as nfzrm');
            //->where('number LIKE ?', '%93500%')->andWhere('importer_id = 1');

        DealerTable::queryByNumber($q);

//        echo '<pre>'. print_r($q->getSqlQuery(), 1) .'</pre>'; die();

        if($this->search_num) {
            if(is_numeric($this->search_num)) {
                $q->andWhere('number LIKE ?', '%' . $this->search_num . '%');
            } else {
                $q->andWhere('name LIKE ?', '%' . $this->search_num . '%');
            }
        }

        if($this->city_id)
            $q->andWhere('city_id = ?', $this->city_id);

        $this->cities = CityTable::getInstance()->createQuery()->select('id, name')->orderBy('name')->execute();

        if($this->direction == 'ASC') { $this->direction = 'DESC'; } else {$this->direction = 'ASC';}
        if ($order) {
            if($order == 'city_id') {
                $q->orderBy('c.name'. ' ' . $this->direction);
            } elseif($order == 'regional_manager_nfz') {
                $q->orderBy('nfzrm.surname'. ' ' . $this->direction);
            } elseif($order == 'regional_manager_pkw') {
                $q->orderBy('rm.surname'. ' ' . $this->direction);
            }
            else {
                $q->orderBy('d.' . $order . ' ' . $this->direction);
            }
        } else {
            $q->orderBy('d.' . $order . ' ' . $this->direction);
        }
//                echo '<pre>'. print_r($q->getSqlQuery(), 1) .'</pre>'; die();

//        $this->dealers = $Dealer->execute();
        $this->dealers = $q->execute();
    }

    public function executeEdit(sfWebRequest $request)
    {
        $this->dealer_id = $request->getGetParameter('id');
        $post = $request->getPostParameters();

        $this->dealer = new Dealer();
        $this->cities = CityTable::getInstance()->createQuery()->select('id, name')->execute();

        if($this->dealer_id)
            $this->dealer = DealerTable::getInstance()->findOneById($this->dealer_id);

        if(!empty($post)) {
            if(empty($post['id'])) {
                if(DealerTable::getInstance()->createQuery()->where('number = ?', $post['number'])->count() > 0) {
                    $this->getUser()->setFlash('error', 'Такой номер дилера ('. $post['number'] .') уже существует в базе.');
                    $this->redirect('/backend.php/dealer_list/edit');
                }
            }

            $this->dealer->setNumber($post['number']);
            $this->dealer->setName($post['name']);
            $this->dealer->setAddress($post['address']);
            $this->dealer->setPhone($post['phone']);
            $this->dealer->setSite($post['site']);
            $this->dealer->setEmail($post['email']);
            $this->dealer->setLongitude($post['longitude']);
            $this->dealer->setLatitude($post['latitude']);
            $this->dealer->setLatitude($post['latitude']);
            $this->dealer->setCityId($post['city_id']);
            $this->dealer->setDealerType($post['dealer_type']);
            $this->dealer->setImporterId(1);
            $this->dealer->setStatus($post['status']);

            if ($post['dealer_type'] == Dealer::TYPE_NFZ_PKW) {
                $this->dealer->setNfzRegionalManagerId($post['nfz_regional_manager_id']);
                $this->dealer->setRegionalManagerId($post['regional_manager_id']);
            } else if ($post['dealer_type'] == Dealer::TYPE_PKW) {
                $this->dealer->setRegionalManagerId($post['regional_manager_id']);
                $this->dealer->setNfzRegionalManagerId(0);
            } else {
                $this->dealer->setNfzRegionalManagerId($post['nfz_regional_manager_id']);
                $this->dealer->setRegionalManagerId(0);
            }

            $this->dealer->save();

            $this->getUser()->setFlash('success', 'Запись успешно сохранена.');
            $this->redirect('/backend.php/dealer_list/edit?id='.$this->dealer->getId());
        }

    }
}
