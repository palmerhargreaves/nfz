<?php

/**
 * Activity filter form.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ActivityFormFilter extends BaseActivityFormFilter
{
  public function configure()
  {
    $this->getWidget('start_date')->setOption('template', 'от %from_date%<br />до %to_date%');    
    $this->getWidget('start_date')->getOption('from_date')->setOption('format', '%day%.%month%.%year%');
    $this->getWidget('start_date')->getOption('to_date')->setOption('format', '%day%.%month%.%year%');
    
    $this->getWidget('end_date')->setOption('template', 'от %from_date%<br />до %to_date%');    
    $this->getWidget('end_date')->getOption('from_date')->setOption('format', '%day%.%month%.%year%');
    $this->getWidget('end_date')->getOption('to_date')->setOption('format', '%day%.%month%.%year%');
    
    $this->getWidget('finished')->setOption('choices', array('' => 'все', 1 => 'завершённые', 0 => 'не завершенённые'));
    $this->getWidget('importance')->setOption('choices', array('' => 'все', 1 => 'влияет', 0 => 'не влияет'));
    $this->getWidget('hide')->setOption('choices', array('' => 'все', 1 => 'скрытые', 0 => 'видимые'));
  }
}
