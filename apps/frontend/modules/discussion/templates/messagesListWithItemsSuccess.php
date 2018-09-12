<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 12.07.2016
 * Time: 11:59
 */
    $pgData = $tab.'_paginatorData';

    include_partial('messages_list', array('pager' => $$tab, 'paginatorData' => $$pgData, 'page_parent' => $page_parent));
?>
