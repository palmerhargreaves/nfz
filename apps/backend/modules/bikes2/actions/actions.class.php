<?php

/**
 * bikes actions.
 *
 * @package    Servicepool2.0
 * @subpackage 
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class bikes2Actions extends sfActions
{
	
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  function executeIndex(sfWebRequest $request)
  {
    $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();

    $dateFrom = strtotime("09.06.2014 00:00:00");
    
    $query = "SELECT * FROM bikes_dealer ORDER BY id DESC";
    $smt = $pdo->prepare($query);

    $result = array();

    $smt->execute();
    $items = $smt->fetchAll();

    $tempId = 0;
    foreach($items as $item) {
      if(strtotime($item['date_of_order']) < $dateFrom)
        continue;

      $dealer = UserTable::getInstance()->find($item['dealer_id']);

      $dealerUser = DealerUserTable::getInstance()->createQuery()->where('user_id = ?', $item['dealer_id'])->fetchOne();
      $dealerNumber = DealerTable::getInstance()->find($dealerUser->getDealerId());

      if(empty($dealer))
        continue;

      if($tempId != $dealer->getId()) {
        $tempId = $dealer->getId();
        
        $result[$item['dealer_id']]['data']['dealer'] = $dealer;
        $result[$item['dealer_id']]['data']['dealerNumber'] = $dealerNumber->getNumber();
        $result[$item['dealer_id']]['data']['item'] = $item;
      }

      $query = "SELECT * FROM bikes WHERE id = :param";
      $smt = $pdo->prepare($query);

      $smt->execute(array("param" => $item['bike_id']));
      $bikeItem = $smt->fetchAll();

      if(empty($bikeItem))
        continue;

      $result[$item['dealer_id']]['data']['bikes'][$bikeItem[0]['ftype']][] = array('name' => $bikeItem[0]['name'], 
                                                                                  'article' => $bikeItem[0]['article'],
                                                                                  'nep' => $bikeItem[0]['nep'],
                                                                                  'rrc' => $bikeItem[0]['rrc'],
                                                                                  'count' => $item['count'],
                                                                                  'ftype' => $bikeItem[0]['ftype']); 

    }

    $this->result = $result;
  
  }
  
  function executeShow(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

  

}
