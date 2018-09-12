<?php

require_once dirname(__FILE__) . '/../lib/activity_video_records_statisticsGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/activity_video_records_statisticsGeneratorHelper.class.php';

/**
 * activity_fields actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_fields
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_video_records_statisticsActions extends autoActivity_video_records_statisticsActions
{
    private $_activity_id = 0;

    public function preExecute()
    {
        //$this->dispatcher->connect('admin.delete_object', array($this, 'onDeleteObject'));

        parent::preExecute();
    }

    function executeIndex(sfWebRequest $request)
    {
        if ($this->_activity_id == 0) {
            $this->getUser()->setAttribute('activity_id', $request->getParameter('activity_id'), ActivityVideoRecordsStatistics::FILTER_NAMESPACE);

            parent::executeIndex($request);
        } else {
            $this->redirect('@activity');
        }
    }

    function executeNew(sfWebRequest $request)
    {
        parent::executeNew($request);

        $activity_id = $this->getUser()->getAttribute('activity_id', -1, ActivityVideoRecordsStatistics::FILTER_NAMESPACE);
        if ($activity_id == 0 || $activity_id == -1) {
            $activity_id = $request->getParameter('activity_id');
        }

        $this->form->bind(array(
            'activity_id' => $activity_id
        ), array());
    }

    function executeDelete(sfWebRequest $request)
    {
        $object_id = $request->getParameter('id');

        $statistic = ActivityVideoRecordsStatisticsTable::getInstance()->find($object_id);
        if ($statistic) {
            $formulas = ActivityEfficiencyFormulasTable::getInstance()->createQuery()->where('activity_id = ?', $statistic->getActivityId())->execute();
            foreach ($formulas as $formula) {
                ActivityEfficiencyFormulaParamsTable::getInstance()->createQuery()->delete()->where('formula_id = ?', $formula->getId())->execute();

                $formula->delete();
            }
        }

        parent::executeDelete($request);
    }

    function redirect($url, $statusCode = 302)
    {
        if ($url == '@activity_video_records_statistics_new' && $this->form) {
            $url .= '?activity_id=' . $this->form->getValue('activity_id');
        }

        parent::redirect($url, $statusCode);
    }

    protected function buildQuery()
    {
        $query = parent::buildQuery();
        if ($this->getUser()->getAttribute('activity_id', -1, ActivityVideoRecordsStatistics::FILTER_NAMESPACE) != -1) {
            $query->andWhere('activity_id = ?', $this->getUser()->getAttribute('activity_id', -1, ActivityVideoRecordsStatistics::FILTER_NAMESPACE));
        }

        return $query->orderBy('id DESC');
    }


    function executeReorder(sfWebRequest $request)
    {
        $data = json_decode($request->getParameter('data'));

        $ind = 1;
        foreach ($data as $key => $data_item) {
            foreach ($data_item as $f_id) {
                if (!empty($f_id)) {
                    $field = ActivityFieldsTable::getInstance()->find($f_id);
                    if ($field) {
                        $field->setPosition($ind);
                        $field->save();

                        $ind++;
                    }
                }
            }
        }

        return sfView::NONE;
    }

    function executeHeadersReorder(sfWebRequest $request)
    {
        $data = json_decode($request->getParameter('data'));

        $ind = 1;
        foreach ($data as $key => $data_item) {
            foreach ($data_item as $f_id) {
                if (!empty($f_id)) {
                    $field = ActivityVideoRecordsStatisticsHeadersTable::getInstance()->find($f_id);
                    if ($field) {
                        $field->setPosition($ind);
                        $field->save();

                        $ind++;
                    }
                }
            }
        }

        return sfView::NONE;
    }

    function executeCopyStatistic(sfWebRequest $request)
    {
        $values = $request->getParameter('values');
        $activity_id = $request->getParameter('activity_id');

        $this->makeActivityFieldsCopy($activity_id, $values);

        return sfView::NONE;
    }

    public function executeCustomCopyStatisticInitData(sfWebRequest $request)
    {
        $values = $request->getParameter('values');
        $activity_id = $request->getParameter('activity_id');

        $this->fields = ActivityFieldsTable::getInstance()->createQuery()->where('activity_id = ?', $activity_id)->orderBy('id ASC')->execute();
        $this->formulas = ActivityEfficiencyFormulasTable::getInstance()->createQuery()->where('activity_id = ?', $activity_id)->orderBy('id ASC')->execute();
    }

    public function executeCustomMakeCopyStatisticData(sfWebRequest $request)
    {
        $fields_to_copy = $request->getParameter('fields_to_copy');
        $formulas_to_copy = $request->getParameter('formulas_to_copy');
        $activity_id = $request->getParameter('activity_id');
        $activities_list = $request->getParameter('activities_list');

        if (!empty($fields_to_copy)) {
            $this->makeActivityFieldsCopy($activity_id, $activities_list, $fields_to_copy, $formulas_to_copy);
        } else {
            foreach ($activities_list as $key => $to_activity) {
                $this->makeFormulasCopy($activity_id, $to_activity, null, $formulas_to_copy);
            }
        }

        return sfView::NONE;
    }

    private function makeActivityFieldsCopy($activity_id, $values, $fields_to_copy = null, $formulas_to_copy = null)
    {
        $old_fields_ids = array();

        $make_formula_copies = false;
        if (is_null($fields_to_copy) && is_null($formulas_to_copy)) {
            $make_formula_copies = true;
        } else if (!is_null($formulas_to_copy)) {
            $make_formula_copies = true;
        }

        $activity_statistic = ActivityVideoRecordsStatisticsTable::getInstance()->createQuery()->where('activity_id = ?', $activity_id)->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
        if ($activity_statistic) {
            $old_activity_statistic_id = $activity_statistic['id'];
            unset($activity_statistic['id']);

            foreach ($values as $key => $to_activity_id) {
                $new_activity_statistic = ActivityVideoRecordsStatisticsTable::getInstance()->createQuery()->where('activity_id = ?', $to_activity_id)->fetchOne();
                if (!$new_activity_statistic) {
                    $activity_statistic['activity_id'] = $to_activity_id;

                    $new_activity_statistic = new ActivityVideoRecordsStatistics();
                    $new_activity_statistic->setArray($activity_statistic);
                    $new_activity_statistic->save();
                }

                $header = ActivityVideoRecordsStatisticsHeadersTable::getInstance()->createQuery()->where('parent_id = ?', $old_activity_statistic_id)->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
                if ($header) {
                    unset($header['id']);

                    $new_activity_statistic_header = ActivityVideoRecordsStatisticsHeadersTable::getInstance()->createQuery()->where('parent_id = ?', $new_activity_statistic->getId())->fetchOne();
                    if (!$new_activity_statistic_header) {
                        $header['parent_id'] = $new_activity_statistic->getId();

                        $new_activity_statistic_header = new ActivityVideoRecordsStatisticsHeaders();
                        $new_activity_statistic_header->setArray($header);
                        $new_activity_statistic_header->save();
                    }

                    if (!is_null($fields_to_copy)) {
                        $fields = ActivityFieldsTable::getInstance()->createQuery()->where('activity_id = ? and group_id = ?', array($activity_id, 0))->andWhereIn('id', $fields_to_copy)->orderBy('id ASC')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                    } else {
                        $fields = ActivityFieldsTable::getInstance()->createQuery()->where('activity_id = ? and group_id = ?', array($activity_id, 0))->orderBy('id ASC')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                    }

                    foreach ($fields as $field) {
                        if (ActivityFieldsTable::getInstance()->createQuery()->where('activity_id = ? and id = ?', array($to_activity_id, $field['id']))->count() > 0) {
                            continue;
                        }

                        $old_field_id = $field['id'];
                        unset($field['id']);

                        $field['activity_id'] = $to_activity_id;
                        $field['parent_header_id'] = $new_activity_statistic_header->getId();

                        $new_field = new ActivityFields();
                        $new_field->setArray($field);
                        $new_field->save();

                        $old_fields_ids[$old_field_id] = $new_field->getId();
                    }

                    if ($make_formula_copies) {
                        $this->makeFormulasCopy($activity_id, $to_activity_id, $old_fields_ids, $formulas_to_copy);
                    }
                }
            }
        }
    }

    private function makeFormulasCopy($activity_id, $to_activity_id, $old_fields_ids, $formulas_to_copy)
    {
        $old_formulas_ids = array();

        if (!is_null($formulas_to_copy)) {
            $formulas = ActivityEfficiencyFormulasTable::getInstance()->createQuery()->where('activity_id = ?', $activity_id)->andWhereIn('id', $formulas_to_copy)->orderBy('id ASC')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        } else {
            $formulas = ActivityEfficiencyFormulasTable::getInstance()->createQuery()->where('activity_id = ?', $activity_id)->orderBy('id ASC')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        }

        foreach ($formulas as $formula) {
            $old_formula_id = $formula['id'];

            unset($formula['id']);
            $formula['activity_id'] = $to_activity_id;
            $formula['status'] = false;

            $new_formula = new ActivityEfficiencyFormulas();
            $new_formula->setArray($formula);
            $new_formula->save();

            $old_formulas_ids[$old_formula_id] = $new_formula->getId();
        }

        foreach ($old_formulas_ids as $old_key => $new_key) {
            $formulas_fields = ActivityEfficiencyFormulaParamsTable::getInstance()->createQuery()->where('formula_id = ?', $old_key)->orderBy('id ASC')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            foreach ($formulas_fields as $formula_field) {
                unset($formula_field['id']);

                $formula_field['formula_id'] = $new_key;
                if (!is_null($old_fields_ids)) {
                    if ($formula_field['param1_type'] == ActivityEfficiencyFormulaParams::FIELD_PARAM) {
                        $formula_field['param1_value'] = isset($old_fields_ids[$formula_field['param1_value']]) ? $old_fields_ids[$formula_field['param1_value']] : 0;
                    } else if ($formula_field['param1_type'] == ActivityEfficiencyFormulaParams::FORMULA_RESULT) {
                        $formula_field['param1_value'] = isset($old_formulas_ids[$formula_field['param1_value']]) ? $old_formulas_ids[$formula_field['param1_value']] : 0;
                    }

                    if ($formula_field['param2_type'] == ActivityEfficiencyFormulaParams::FIELD_PARAM) {
                        $formula_field['param2_value'] = isset($old_fields_ids[$formula_field['param2_value']]) ? $old_fields_ids[$formula_field['param2_value']] : 0;
                    } else if ($formula_field['param2_type'] == ActivityEfficiencyFormulaParams::FORMULA_RESULT) {
                        $formula_field['param2_value'] = isset($old_formulas_ids[$formula_field['param2_value']]) ? $old_formulas_ids[$formula_field['param2_value']] : 0;
                    }

                    $new_formula_param = new ActivityEfficiencyFormulaParams();
                    $new_formula_param->setArray($formula_field);
                    $new_formula_param->save();
                }
            }
        }
    }
}
