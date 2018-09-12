<?php

/**
 * home actions.
 *
 * @package    Servicepool2.0
 * @subpackage home
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class homeActions extends sfActions
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */

    function executeIndex(sfWebRequest $request)
    {
        $this->getUser()->setAttribute('editor_link', null);

        $this->year = D::getBudgetYear($request);
        $this->budgetYears = D::getBudgetYears($request);

        if ($this->getUser()->isDealerUser()) {

            if ($this->getUser()->getAuthUser()->isUserCertificateActive() && !$this->getUser()->getAttribute('msg', false)) {
                $this->getUser()->setAttribute('msg', true);
                $this->redirect('@homepage?msg=yes' . $this->makeReqYear());
            }

            $service = $request->getParameter('service');
            //if (DealerServicesDialogsTable::isActiveForUser($this->getUser()->getAuthUser()) && !$this->getUser()->getAttribute('service', false)) {
            if (DealerServicesDialogsTable::isActiveForUser($this->getUser()->getAuthUser()) && empty($service)) {
                $this->getUser()->setAttribute('service', true);
                $this->redirect('@homepage?service=yes' . $this->makeReqYear());
            } else if (DialogsTable::getLastActiveInfoDialog() && !$this->getUser()->getAttribute('info', false)) {
                $this->getUser()->setAttribute('info', true);
                $this->redirect('@homepage?info=yes' . $this->makeReqYear());
            }


            /*if(!$this->getUser()->getAttribute('tour', false))
            {
              $this->getUser()->setAttribute('tour', true);
              $this->redirect('@homepage?start-tour=yes');
            }*/


        } elseif ($this->getUser()->isImporter() && !$this->getUser()->isManager()) {
            //$this->redirect('@agreement_module_activities_status');
            if ($this->getUser()->setAttribute('login', false)) {
                $this->getUser()->setAttribute('login', true);
                $this->redirect('@homepage');
            }
        }

    }

    function executeImportUsers(sfWebRequest $request)
    {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle('Users');

        $headers = array('Дилер', 'Группа', 'Email', 'Имя', 'Фамилия', 'Должность', 'Активен');
        $column = 0;
        $row = 0;

        //настройки для шрифтов
        $baseFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => false
            )
        );
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

        /*$aSheet->getStyle('A1:G1')->applyFromArray($boldFont);
        $aSheet->getStyle('B:G')->applyFromArray($center);*/

        $column = 0;
        $tCount = 1;
        /*foreach($headers as $head) {


          $aSheet->setCellValueByColumnAndRow($column++, 1, $head);
          $tCount++;
        }*/

        $aSheet->getColumnDimension('A')->setWidth(10);
        $aSheet->getColumnDimension('B')->setWidth(30);
        $aSheet->getColumnDimension('C')->setWidth(35);
        $aSheet->getColumnDimension('D')->setWidth(30);
        $aSheet->getColumnDimension('E')->setWidth(30);
        $aSheet->getColumnDimension('F')->setWidth(30);
        $aSheet->getColumnDimension('G')->setWidth(30);
        $aSheet->getColumnDimension('H')->setWidth(30);
        $aSheet->getColumnDimension('I')->setWidth(30);
        $aSheet->getColumnDimension('J')->setWidth(30);

        $row = 1;
        $column = 0;
        $tCount = 1;

        $users = UserTable::getInstance()->createQuery()->select()->orderBy('id ASC')->execute();
        foreach ($users as $user) {
            $column = 0;

            $dealer = $user->getDealerUsers()->getFirst();
            if (empty($dealer))
                continue;

            $dealer = $dealer->getDealer();

            $aSheet->setCellValueByColumnAndRow($column++, $row, sprintf('%s', $user->getId()));
            $aSheet->setCellValueByColumnAndRow($column++, $row, sprintf('%s %s', $user->getSurname(), $user->getName()));
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getEmail());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getPassword());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer->getName());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer->getCity()->getName());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $user->getPhone());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer->getAddress());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer->getSite());
            $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer->getPhone());


            $row++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save(sfConfig::get('sf_root_dir') . '/www/uploads/usersDealers.xls');

        $this->redirect('http://dm.vw-servicepool.ru/uploads/usersDealers.xls');
    }

    function executeSpecialAccept(sfWebRequest $request)
    {
        $act = $request->getParameter('act');
        $user = $this->getUser()->getAuthUser();

        if ($act == 'accept') {
            $budget = $request->getParameter('budget');
            $sum = $request->getParameter('sum');

            $user->setSpecialBudgetQuater($budget);
            $user->setSpecialBudgetSumm($sum);
            $user->setSpecialBudgetStatus(1);
            $user->setSpecialBudgetDateOf(date('d-m-Y'));

        } else {
            $user->setSpecialBudgetStatus(2);
            $user->setSpecialBudgetDateOf(date('d-m-Y'));
        }

        $result = array('status' => -1);
        if ($user->save()) {
            $result['status'] = 1;
        }

        $this->getResponse()->setContentType('application/json');
        $this->getResponse()->setContent(json_encode(array($result)));

        return sfView::NONE;
    }

    function executeSummerSpecial(sfWebRequest $request)
    {
        $user = $this->getUser()->getAuthUser();
        $startDate = $request->getParameter('startDate');
        $endDate = $request->getParameter('endDate');

        $user->setSummerActionStartDate(str_replace('.', '-', $startDate));
        $user->setsummerActionEndDate(str_replace('.', '-', $endDate));

        $result = array('status' => -1);
        if ($user->save())
            $result['status'] = 1;

        $this->getResponse()->setContentType('application/json');
        $this->getResponse()->setContent(json_encode(array($result)));

        return sfView::NONE;
    }

    function executeSummerServiceAction(sfWebRequest $request)
    {


    }

    function executeGazetaFile(sfWebRequest $request)
    {
        $file = $this->getUser()->getAuthUser()->getGazetaFiles();

        $dealer = $this->getUser()->getAuthUser()->getDealerUsers()->getFirst();
        if (!$dealer) {
            return true;
        }

        $dealer = DealerTable::getInstance()->createQuery('d')->where('id = ?', $dealer->getDealerId())->fetchOne();
        $dealerNumber = substr($dealer->getNumber(), 5);

        $gazeta = new GazetaFiles();
        $gazeta->setDealerIndex($dealerNumber);
        $gazeta->setFileName($file);

        $gazeta->save();

        $this->redirect('http://dm.vw-servicepool.ru/uploads/gazeta/' . $file);
    }

    function executeGazetaOffsetFile(sfWebRequest $request)
    {
        $file = $this->getUser()->getAuthUser()->getGazetaOffsetFiles();

        $dealer = $this->getUser()->getAuthUser()->getDealerUsers()->getFirst();
        if (!$dealer) {
            return true;
        }

        $this->redirect('http://dm.vw-servicepool.ru/uploads/gazeta/' . $file);
    }

    function executeServiceAction(sfWebRequest $request)
    {
        $user = $this->getUser()->getAuthUser();
        $dealer = $user->getDealerUsers()->getFirst();

        $startDate = $request->getParameter('startDate');
        $endDate = $request->getParameter('endDate');
        $dialogId = $request->getParameter('dialogId');
        $actType = $request->getParameter('actType');

        $temp = new DealersServiceData();

        $temp->setUserId($user->getId());
        $temp->setDealerId($dealer->getDealerId());
        $temp->setDialogServiceId($dialogId);

        if (!empty($startDate) && !empty($endDate)) {
            $temp->setStartDate(str_replace('.', '-', $startDate));
            $temp->setEndDate(str_replace('.', '-', $endDate));
        }

        $temp->setStatus($actType == 'accept' ? 'accepted' : 'declined');
        $temp->save();

        $dialog = DealerServicesDialogsTable::getInstance()->find($dialogId);

        $this->getResponse()->setContentType('application/json');
        $this->getResponse()->setContent(json_encode(array('status' => 1, 'msg' => $dialog->getSuccessMsg())));

        return sfView::NONE;
    }

    function executeAcceptUserPost(sfWebRequest $request)
    {
        $dep = array(1 => 'Отдел сервиса', 2 => 'Отдел маркетинга', 3 => 'Отдел продаж запчастей и аксессуаров', 4 => 'Генеральный директор');

        $department = $request->getParameter('department');
        $post = $department == 4 ? $dep[$department] : $request->getParameter('userPost');

        $user = $this->getUser()->getAuthUser();
        $user->setPost($post);
        $user->save();

        $userPost = new UsersPost();
        $userPost->setDepartment($dep[$department]);
        $userPost->setPost($post);
        $userPost->setUserId($this->getUser()->getAuthUser()->getId());

        $userPost->save();

        return sfView::NONE;
    }

    function executeShowServiceDialog(sfWebRequest $request)
    {
        $id = $request->getParameter('id');

        $this->data = DealerServicesDialogsTable::getInstance()->find($id);
    }

    private function makeReqYear()
    {
        if (!empty($this->year)) {
            return "&year=" . $this->year;
        }

        return "";
    }

    function executeGenFiles() {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle('Users');

        $headers = array('Пользователь', 'Привязки До', 'Привязки После');
        $column = 0;
        $row = 0;

        //настройки для шрифтов
        $baseFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => false
            )
        );
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

        /*$aSheet->getStyle('A1:G1')->applyFromArray($boldFont);
        $aSheet->getStyle('B:G')->applyFromArray($center);*/

        $column = 0;
        $tCount = 1;
        foreach($headers as $head) {


            $aSheet->setCellValueByColumnAndRow($column++, 1, $head);
            $tCount++;
        }

        $aSheet->getColumnDimension('A')->setWidth(50);
        $aSheet->getColumnDimension('B')->setWidth(50);
        $aSheet->getColumnDimension('C')->setWidth(55);


        $row = 3;
        $column = 0;
        $tCount = 1;

        $fillColor = "BB8300";

        $users = UserTable::getInstance()->createQuery()->select()->orderBy('id ASC')->execute();
        foreach ($users as $user) {
            $column = 0;

            $aSheet->setCellValueByColumnAndRow($column++, $row, sprintf('[%s] %s', $user->getId(), $user->getEmail()));
            $dealer_text = '';
            $dealers_ids = '';
            foreach ($user->getDealerUsers() as $dealer) {
                $dealer_text .= sprintf('[%s] %s (%s) | ', $dealer->getDealer()->getShortNumber(), $dealer->getDealer()->getName(), $dealer->getDealer()->getDealerTypeLabel());
                $dealers_ids .= $dealer->getDealer()->getId();
            }
            $aSheet->setCellValueByColumnAndRow($column, $row, $dealer_text);

            $column++;
            $dealer_text = '';
            $dealers_ids_eq = '';
            foreach (DealerUserOldTable::getInstance()->createQuery()->where('user_id = ?', $user->getId())->execute() as $dealer) {
                $dealer_text .= sprintf('[%s] %s (%s) | ', $dealer->getDealer()->getShortNumber(), $dealer->getDealer()->getName(), $dealer->getDealer()->getDealerTypeLabel());
                $dealers_ids_eq .= $dealer->getDealer()->getId();
            }

            $aSheet->setCellValueByColumnAndRow($column, $row, $dealer_text);

            if ($dealers_ids != $dealers_ids_eq) {
                $aSheet->getStyle('A' . $row . ':C' . $row)
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB($fillColor);
            }

            $row++;
        }

        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save(sfConfig::get('sf_root_dir') . '/www/uploads/nfz_users_dealers.xls');

        $this->redirect('http://nfz.vw-servicepool.ru/uploads/nfz_users_dealers.xls');
    }
}
