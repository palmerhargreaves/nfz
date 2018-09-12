<?php

/**
 * agreement_activity_config actions.
 *
 * @package    Servicepool2.0
 * @subpackage agreement_activity_config
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agreement_activity_configActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('activity_id', $request->getParameter('activity_id'), 'agreement_module');
    $this->redirect('@agreement_model_blank');
  }
}
