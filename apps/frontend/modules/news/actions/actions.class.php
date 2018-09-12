<?php

/**
 * news actions.
 *
 * @package    Servicepool2.0
 * @subpackage news
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class newsActions extends sfActions
{
    function executeIndex(sfWebRequest $request)
    {
        $this->news = $this->getNewsList();
    }

    function executeNewsInfo(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $today = $this->getElapsedDays(strtotime(date('d-m-Y')));

        $query = NewsTable::getInstance()->createQuery()->select('*')->where('id = ?', $id);
        $this->selected_news_item = $query->fetchOne();

        $this->news = $this->getNewsList();

        $this->setTemplate('index');

    }

    private function getNewsList() {
        $result = array();
        //$news = NewsTable::getInstance()->createQuery()->select('*')->where('status = ? and year(created_at) = ?', array(true, date('Y')))->orderBy('id DESC')->execute();
        $news = NewsTable::getInstance()->createQuery()->select('*')->where('status = ?', array(true))->orderBy('id DESC')->execute();

        $days = 0;
        $today = $this->getElapsedDays(strtotime(date('d-m-Y')));

        if (count($news) > 0) {
            $lastDate = date('d-m-Y', strtotime($news->getFirst()->getCreatedAt()));

            foreach ($news as $item) {
                $isNew = false;
                $createdAt = $this->getElapsedDays(strtotime($item->getCreatedAt()));
                $tempDate = date('d-m-Y', strtotime($item->getCreatedAt()));

                $elDays = $today - $createdAt;
                if ($elDays < 30 && $days < 3 && ($lastDate == $tempDate)) {
                    $isNew = true;
                }

                if (isset($this->selected_news_item) && $item->getId() == $this->selected_news_item->getId()) {
                    continue;
                }

                $result[] = array("item" => $item, "isNew" => $isNew);
                $days++;
            }
        }

        return $result;
    }

    function getElapsedDays($st)
    {
        return floor(($st / 3600) / 24);
    }
}
