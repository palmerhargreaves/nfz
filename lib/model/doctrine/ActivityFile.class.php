<?php

/**
 * ActivityFile
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ActivityFile extends BaseActivityFile
{
  const FILE_PATH = 'activities/file';
  
  function setUp()
  {
    parent::setUp();
    
    $this->addListener(new UploadHelper('file', self::FILE_PATH));
  }
  
  /**
   * Returns a file name helper
   * 
   * @return FileNameHelper
   */
  function getFileNameHelper()
  {
    return new FileNameHelper(sfConfig::get('sf_upload_dir').'/'.self::FILE_PATH.'/'.$this->getFile());
  }
}