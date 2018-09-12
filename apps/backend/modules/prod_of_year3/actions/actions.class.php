<?php

/**
 * prod_of_year3 actions.
 *
 * @package    Servicepool2.0
 * @subpackage prod_of_year3
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class prod_of_year3Actions extends sfActions
{
	
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  function executeIndex(sfWebRequest $request)
  {
    $result = UserTable::getUsersWithProdOfYear3();

    $this->result = array('total' => count($result), 'items' => $result);
  
  }
  
  function executeShow(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

  function executeAdd(sfWebRequest $request)  
  {
    $result = array();

    $dealers = DealerTable::getInstance()->getDealersList()->execute();
    foreach($dealers as $dealer) {
      $res = DealerUserProdOfYear3Table::getInstance()->createQuery()->select()->where('dealer_id = ?', $dealer->getId())->count();
      
      if($res == 0)
        $result[] = $dealer;
    }

    $this->dealers = $result;
  }

  function executePostData(sfWebRequest $request)
  {
    $dealerId = $request->getPostParameter('sb_dealer');
    
    $item = new DealerUserProdOfYear3();

    $item->setUserId($this->getUser()->getAuthUser()->getId());
    $item->setDealerId($dealerId);
    
    $item->save();
    $this->redirect('@prod_of_year_3');
  }

  function executeDeleteProd(sfWebRequest $request)
  {
    DealerUserProdOfYear3Table::getInstance()->createQuery()->where('id = ?', $request->getParameter('id'))->delete()->execute();
    
    $this->redirect('@prod_of_year_3');    
  }

}
