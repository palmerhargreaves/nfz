<?php

/**
 * makets_info actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class makets_infoActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  private $filesDir = '/activities/module/agreement/model_file/';

  const START_YEAR = 2013;

  function executeIndex(sfWebRequest $request)
  {
    $year = $request->getParameter('year');
    $quarter = $request->getParameter('quarter');
    $dealer = $request->getParameter('dealer_filter');
    $activity = $request->getParameter('activity');
    $fTypes = $request->getParameter('fTypes');
    $onlyAccepted = $request->getParameter('cbOnlyAcceptModels') == "on" ? true : false;
    
    $this->currentYear = (empty($year) ? date('Y') : $year);
    $this->currentActivity = $activity;
    $this->dealer_filter = $dealer;
    $this->fType = $fTypes;
    $this->currentQuarter = $quarter;
    $this->onlyAccepted = $onlyAccepted;

    $this->startYear = self::START_YEAR;
    $this->endYear = date('Y');
        
    if($year) {
      
      $zip = new ZipArchive();
      $zipFile = sfConfig::get('sf_upload_dir').DIRECTORY_SEPARATOR.'makets.zip';
    
      @unlink($zipFile);
      $res = $zip->open($zipFile, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);

      if($res) {
        
        $activities = array();
        $dealers = array();

        $query = AgreementModelTable::getInstance()
                    ->createQuery('m')
                      ->select('*')
                        ->leftJoin('m.Report r')
                        ->where('m.status = ?', array('accepted'))
                        ->orderBy('m.activity_id ASC');

        if(!empty($dealer) && $dealer != -1) 
          $query->andWhere('m.dealer_id = ?', $dealer);

        $query->andWhere('m.created_at LIKE ?', $year.'%');
        
        if(!empty($activity) && $activity != -1)
          $query->andWhere('m.activity_id = ?', $activity);

        if(!empty($fTypes) && $fTypes != -1)
          $query->andWhere('m.model_file LIKE ?', '%'.$fTypes.'%'); 
        /*else
          $query->andWhere('m.model_file LIKE ? or m.model_file LIKE ?', array('%.mp3%', '%.swf%'));*/

        if($onlyAccepted) {
          $query->andWhere('r.status = ?', 'accepted'); 
        }

        if($quarter != 0 && !$onlyAccepted) 
          $query->andWhere('quarter(m.created_at) = ?', $quarter);
        
        $result = $query->execute();
        foreach($result as $item) 
        {
          if($onlyAccepted && $quarter > 0) {
            $itemQuarter = D::getQuarter(D::calcQuarterData($item->getReport()->getUpdatedAt()));

            if($quarter != $itemQuarter) {
              continue;
            }
          }

          $activity = $this->normalize($item->getActivity()->getName());
          $dealer = $this->normalize($item->getDealer()->getName());
          $fileExt = pathinfo($item->getModelFile(), PATHINFO_EXTENSION );
          
          /*if(!in_array($dealer, $dealers)) {
            $dealers[] = $dealer;

            $zip->addEmptyDir($dealer);
          }*/
          if(!in_array($activity, $activities)) {
            $dealers[] = $activity;

            $zip->addEmptyDir($activity);
          }

          $info = pathinfo($item->getModelFile());
          $fileInfo = sprintf('[%s] %s.%s', $item->getId(), $info['filename'], $info['extension']);

          $zip->addFile(sfConfig::get('sf_upload_dir').$this->filesDir.DIRECTORY_SEPARATOR.$item->getModelFile(), $activity.'/'.$dealer.'/'.$fileExt.'/'.$fileInfo);
        }

        $res = $zip->close();
      }
    }

    if($res && count($result) > 0) {
      $this->redirect('/uploads/makets.zip');

      $this->status = true;
    }
    else
      $this->status = false;
    
    
    $this->dealers = DealerTable::getVwDealersQuery()->execute();
    $this->activities = ActivityTable::getInstance()
                            ->createQuery()
                            ->orderBy('position ASC')
                              ->execute();
    $this->filesTypes = $this->getFilesTypes($year);
    
  }

  function getFilesTypes($year)
  {
    if(empty($year))
      $year = date('Y');

    $types = array();
    $query = AgreementModelTable::getInstance()
                    ->createQuery('m')
                      ->select('*')
                        ->where('m.status = ?', array('accepted'));
    $query->andWhere('m.created_at LIKE ?', $year.'%');

    $result = $query->execute();
    foreach($result as $res) {
      $fileExt = pathinfo($res->getModelFile(), PATHINFO_EXTENSION );

      if(!in_array($fileExt, $types))
        $types[] = $fileExt;
    }

    sort($types);

    return $types;
  }
  
  function executeShow(sfWebRequest $request)
  {
    
    $this->setTemplate('index');
  }


  function normalize($name)
  {
    $str = '';
    $name = mb_strtolower($this->toUtf8($name), 'UTF-8'); 

    for($n = 0, $len = mb_strlen($name, 'UTF-8'); $n < $len; $n ++)
    {
      $new_sym = $sym = mb_substr($name, $n, 1, 'UTF-8');
      if(!$this->isSymEnabled($sym))
      {
        $new_sym = $this->symToTranslit($sym);
        if(!$new_sym)
          $new_sym = '_';
      }

      $str .= $new_sym;
    }

    return $str;
  }

  function isSymEnabled($sym)
  {
    $enabled = 'abcdefghijklmnopqrstuvwxyz0123456789';
    return mb_strpos($enabled, $sym, 0, 'UTF-8') !== false;
  }
  
  function symToTranslit($sym)
  {
    static $translit = array(
      'а' => 'a',
      'б' => 'b',
      'в' => 'v',
      'г' => 'g',
      'д' => 'd',
      'е' => 'e',
      'ё' => 'yo',
      'ж' => 'zh',
      'з' => 'z',
      'и' => 'i',
      'й' => 'j',
      'к' => 'k',
      'л' => 'l',
      'м' => 'm',
      'н' => 'n',
      'о' => 'o',
      'п' => 'p',
      'р' => 'r',
      'с' => 's',
      'т' => 't',
      'у' => 'u',
      'ф' => 'f',
      'х' => 'h',
      'ц' => 'c',
      'ч' => 'ch',
      'ш' => 'sh',
      'щ' => 'sch',
      'ы' => 'yi',
      'э' => 'ye',
      'ю' => 'yu',
      'я' => 'ya'
    );
    
    return isset($translit[$sym]) ? $translit[$sym] : false;
  }  

  function toUtf8($name)
  {
    return mb_convert_encoding($name, 'UTF-8', 'UTF-8,CP1251,ASCII'); 
  }

}
