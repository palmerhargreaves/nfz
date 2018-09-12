<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 11.05.2016
 * Time: 12:50
 */

if ($model && ($report = $model->getReport())) {
    if ($report_file_type == 'fin') {
        include_partial('financial_block', array('model' => $model, 'report' => $report));
    } else if ($report_file_type == 'add') {
        include_partial('additional_block', array('model' => $model, 'report' => $report));
    }
}