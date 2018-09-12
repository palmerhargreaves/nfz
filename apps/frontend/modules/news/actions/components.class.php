<?php

class newsComponents extends sfComponents
{
    function executeLastNews()
    {
        $news = NewsTable::getInstance()
            ->createQuery()
            ->where('status = ?', true)
            ->orderBy('id DESC')
            ->limit(3)
            ->execute();

        $days = 0;
        $today = D::getElapsedDays(strtotime(date('d-m-Y')));

        $result = array();
        if (count($news) > 0) {
            $lastDate = date('d-m-Y', strtotime($news->getFirst()->getCreatedAt()));
            foreach ($news as $item) {
                $isNew = false;
                $createdAt = D::getElapsedDays(strtotime($item->getCreatedAt()));
                $tempDate = date('d-m-Y', strtotime($item->getCreatedAt()));

                $elDays = $today - $createdAt;
                if ($elDays < 30 && $days < 3 && ($lastDate == $tempDate))
                    $isNew = true;

                $result[] = array("item" => $item, "isNew" => $isNew);

                $days++;
            }
        }

        $this->lastNews = $result;
    }
}