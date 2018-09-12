<?php

/**
 * Dealer filter form.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class DealerFormFilter extends BaseDealerFormFilter
{
  public function configure()
  {
    $this->getWidget('number')->setOption('with_empty', false);
    $this->getWidget('name')->setOption('with_empty', false);
    $this->getWidget('address')->setOption('with_empty', false);
    $this->getWidget('phone')->setOption('with_empty', false);
    $this->getWidget('site')->setOption('with_empty', false);
    $this->getWidget('email')->setOption('with_empty', false);
  }
}
