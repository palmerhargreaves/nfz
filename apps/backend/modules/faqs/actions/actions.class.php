<?php

require_once dirname(__FILE__).'/../lib/faqsGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/faqsGeneratorHelper.class.php';

/**
 * faqs actions.
 *
 * @package    Servicepool2.0
 * @subpackage faqs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class faqsActions extends autoFaqsActions
{
    protected function buildQuery()
    {
        $ind = 1;
        $query = parent::buildQuery();

        $query->orderBy('id DESC');
        $items = $query->execute();
        foreach ($items as $item) {
            if ($item->getPosition() == -999) {
                $item->setPosition($ind);
                $item->save();

                $ind++;
            }
        }

        $query = parent::buildQuery();
        return $query->orderBy('position ASC');
    }

    function executeReorder(sfWebRequest $request)
    {
        $data = json_decode($request->getParameter('data'));

        $ind = 1;
        foreach ($data->{'faqs-list'} as $key) {
            if (!empty($key) && is_numeric($key)) {
                $faq = FaqsTable::getInstance()->find($key);
                if ($faq) {
                    $faq->setPosition($ind);
                    $faq->save();
                }

                $ind++;
            }
        }

        return sfView::NONE;
    }
}
