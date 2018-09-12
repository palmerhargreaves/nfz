<?php

include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel.php');
include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel/Cell.php');
include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel/Writer/Excel5.php');

/**
 * comment_stat actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_statsActions extends ActionsWithJsonForm
{
    const FILTER_NAMESPACE = 'simple';

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */

    function executeIndex(sfWebRequest $request)
    {
        $this->outputActivities();
        $this->outputFilters($request);

        $this->outputData();
    }

    /**
     * Получаем данные по активностям
     */
    private function outputActivities() {
        $this->activities = ActivityTable::getInstance()->createQuery('a')->select()->orderBy('id DESC')->execute();
    }

    /**
     * Получаем данные по фильтрам
     * @param sfWebRequest $request
     */
    private function outputFilters(sfWebRequest $request) {
        $this->activity_filter = $request->getParameter('activity_filter');
        $this->activity_filter_quarter = $request->getParameter('filter_by_quater');
        $this->activity_filter_month = $request->getParameter('filter_by_month');
        $this->activity_report_complete = $request->getParameter('report_complete');
        $this->check_quarter_by_calendar_filter = $request->getParameter('check_quarter_by_calendar');

        $this->activity_filter_year = $request->getParameter('filter_by_year');
        if (!$this->activity_filter_year) {
            $this->activity_filter_year = date('Y');
        }

        $this->activity_filter_redactor = $request->getParameter('work_in_redactor');
    }

    /**
     * Вывод информации по заявкам
     */
    private function outputData() {
        if ($this->activity_filter) {
            $query = AgreementModelTable::getInstance()
                ->createQuery('m')
                ->innerJoin('m.LogEntry log')
                ->select('m.*, r.status')
                ->leftJoin('m.Report r');

            //Фильтр по всем активностям или только по выбранной
            if ($this->activity_filter != -1) {
                $query->where('activity_id = ?', $this->activity_filter);
            }

            //Фильтр данных только для заявок без согласованного очета (если в фильтре не стоит галочка Выполненные отчеты)
            if (!$this->activity_report_complete) {
                //Сортируем по первой записи в логах
                $query->orderBy('log.id ASC');

                $query->andWhereIn('log.object_type', array('agreement_model'))
                    ->andWhereIn('log.action', array('add'));

                //Учет по кварталу
                $this->filterByQuarter($query);
            } else {
                //Сортируем по последней записи в логах (хотя это не обязательно, так как согласование у отчета возможжно только одно)
                $query->orderBy('log.id DESC');

                $query->andWhereIn('log.object_type', array('agreement_report'))
                    ->andWhereIn('log.action', array('accepted'));

                //Учет по кварталу
                $this->filterByQuarter($query);

                $query->andWhere('m.status = ? and r.status = ?', array('accepted', 'accepted'));
            }

            //Фильтр по месяцу
            if ($this->activity_filter_month && $this->activity_filter_month != -1) {
                $query->andWhere('log.created_at LIKE ?', '%' . date($this->activity_filter_year . '-' . $this->activity_filter_month) . '%');
            }

            //Фильтр по году
            if ($this->activity_filter_year) {
                $query->andWhere('year(log.created_at) = ?', $this->activity_filter_year);
            }

            if ($this->activity_filter_redactor) {
                $query->andWhere('m.model_accepted_in_online_redactor = ?', 1);
            }

            $this->models = $query->execute();
        }
    }

    /**
     * Фильтр данных по быбранному кварталу с учетом бюджетного календаря
     * @param $query
     */
    private function filterByQuarter(&$query) {
        if ($this->activity_filter_quarter) {
            //Если необходимо учитывать дату начала квартала, получаем начало квартала с базы иначе просто учитываем весь квартал

            if ($this->check_quarter_by_calendar_filter && $this->check_quarter_by_calendar_filter && $this->activity_filter_quarter != -1) {
                $quarter_start_date = BudgetCalendarTable::getInstance()->createQuery()->where('year = ? and quarter = ?', array($this->activity_filter_year, $this->activity_filter_quarter))->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
                if ($quarter_start_date) {
                    $quarter_months = D::getQuarterMonths($this->activity_filter_quarter);

                    $start_month = array_shift($quarter_months);
                    $end_month = array_pop($quarter_months);

                    $start_month_date = date('Y-m-d', mktime(0,0,0, $start_month, $quarter_start_date['day'], $this->activity_filter_year));
                    $end_month_date = date('Y-m-d', mktime(0,0,0, $end_month + 1, 1, $this->activity_filter_year));

                    $query->andWhere('log.created_at >= ? and log.created_at < ?', array($start_month_date, $end_month_date));
                }
            } else if ($this->activity_filter_quarter != -1) {
                $query->andWhere('quarter(log.created_at) = ?', $this->activity_filter_quarter);
            }
        }
    }

    function executeExportToExcel(sfWebRequest $request)
    {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle('Список заявок');

        $this->outputActivities();
        $this->outputFilters($request);

        $this->outputData();

        $headers = array('Дилер (название и номер)', 'Номер макета', 'Название макета', 'Тип', 'Размер (если есть)', 'Период');

        $boldFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => true
            )
        );
        $center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
            )
        );

        $aSheet->getStyle('A1:G1')->applyFromArray($boldFont);
        $aSheet->getStyle('B:G')->applyFromArray($center);

        $column = 0;
        $tCount = 1;
        foreach ($headers as $head) {
            $aSheet->setCellValueByColumnAndRow($column++, 1, $head);
            $tCount++;
        }

        $aSheet->getColumnDimension('A')->setWidth(40);
        $aSheet->getColumnDimension('B')->setWidth(20);
        $aSheet->getColumnDimension('C')->setWidth(35);
        $aSheet->getColumnDimension('D')->setWidth(20);
        $aSheet->getColumnDimension('E')->setWidth(20);
        $aSheet->getColumnDimension('F')->setWidth(50);
        $aSheet->getColumnDimension('G')->setWidth(20);

        $row = 2;
        $tCount = 1;
        foreach ($this->models as $model) {
            $column = 0;

            $dealer = $model->getDealer();

            $fields = AgreementModelFieldTable::getInstance()->createQuery()->select()->where('model_type_id = ?', $model->getModelType()->getId())->andWhere('identifier = ? or identifier = ?', array('period', 'size'))->execute();

            $aSheet->setCellValueByColumnAndRow($column++, $row, sprintf('%s (%s)', $dealer->getName(), $dealer->getNumber()));
            $aSheet->setCellValueByColumnAndRow($column++, $row, $model->getId());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $model->getName());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $model->getModelType()->getIdentifier());

            $val = "";
            foreach ($fields as $field) {
                if ($field->getIdentifier() == 'size') {
                    $value = AgreementModelValueTable::getInstance()->createQuery()->select()->where('model_id = ? and field_id = ?', array($model->getId(), $field->getId()))->fetchOne();

                    if ($value)
                        $val = $value->getValue();
                }
            }

            $aSheet->setCellValueByColumnAndRow($column++, $row, $val);

            $val = "";
            foreach ($fields as $field) {
                if ($field->getIdentifier() == 'period') {
                    $value = AgreementModelValueTable::getInstance()->createQuery()->select()->where('model_id = ? and field_id = ?', array($model->getId(), $field->getId()))->fetchOne();

                    if ($value)
                        $val = $value->getValue();
                }
            }

            $aSheet->setCellValueByColumnAndRow($column, $row, $val);

            $aSheet->getStyle('A' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('B' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('C' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('D' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('E' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('F' . $tCount)->applyFromArray($center);
            $aSheet->getStyle('G' . $tCount)->applyFromArray($center);

            $aSheet->getStyle('B' . $tCount . ':G' . $tCount)->applyFromArray($center);

            $row++;
            $tCount++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($pExcel, 'Excel2007');
        $objWriter->save(sfConfig::get('sf_root_dir') . '/www/uploads/models.xlsx');

        return $this->sendJson(array('success' => true, 'url' => 'http://nfz.vw-servicepool.ru/uploads/models.xlsx'));
    }

    function executeShow(sfWebRequest $request)
    {
        $this->setTemplate('index');
    }


}
