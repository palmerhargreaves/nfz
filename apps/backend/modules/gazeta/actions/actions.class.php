<?php

/**
 * gazeta actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class gazetaActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  function executeIndex(sfWebRequest $request)
  {
    $this->result = GazetaFilesTable::getInstance()->createQuery()->select('*')->groupBy('dealer_index')->orderBy('id DESC')->execute();
  }
  
  function executeShow(sfWebRequest $request)
  {

    $this->setTemplate('index');
  }

  
}
