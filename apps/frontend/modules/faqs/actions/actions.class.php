<?php

/**
 * faqs actions.
 *
 * @package    Servicepool2.0
 * @subpackage faqs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class faqsActions extends sfActions
{
    function executeIndex(sfWebRequest $request)
    {
        $this->faqs = FaqsTable::getInstance()->createQuery()->select('*')->where('status = ?', array(true))->orderBy('position ASC')->execute();;
    }
}
