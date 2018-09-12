<?php

require_once dirname(__FILE__) . '/../lib/activity_fieldsGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/activity_fieldsGeneratorHelper.class.php';

/**
 * activity_fields actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_fields
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_fieldsActions extends autoActivity_fieldsActions
{
    function executeIndex(sfWebRequest $request)
    {
        $this->redirect('@activity');
    }

    function executeNew(sfWebRequest $request)
    {
        parent::executeNew($request);

        $this->form->bind(array(
            'activity_id' => $request->getParameter('activity_id')
        ), array());
    }

    function redirect($url, $statusCode = 302)
    {
        if ($url == '@activity_fields_new' && $this->form)
            $url .= '?activity_id=' . $this->form->getValue('activity_id');

        parent::redirect($url, $statusCode);
    }
}
