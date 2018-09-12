<?php

/**
 * activity_extended_statistic actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity_extended_statistic
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activity_extended_statisticActions extends sfActions
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    const MAIL_TEMPLATE_DEALER_NAME = '{dealer_name}';
    const MAIL_TEMPLATE_DEALER_DATE = '{date}';
    const MAIL_TEMPLATE_DEALER_DATE_MONTH = '{date_month}';

    function executeIndex(sfWebRequest $request)
    {
        $this->outputSections();
        $this->outputFields();
        $this->activities = ActivityTable::getInstance()->createQuery()->where('allow_extended_statistic = ?', true)->orderBy('position ASC')->execute();
    }

    function executeLoad(sfWebRequest $request)
    {
        $this->outputSections(null, $request->getParameter('id'));
        $this->outputFields(null, $request->getParameter('id'));
        $this->outputCertificatedData($request->getParameter('id'));

        $this->mailDealerList = ActivityDealerMailsTable::getInstance()->createQuery()->where('activity_id = ?', $request->getParameter('id'))->orderBy('id ASC')->execute();

        $this->statistic = new ActivityExtendedStatisticsBuilder();
        $this->activity = $request->getParameter('id');
    }

    //Add new section
    function executeAddSection(sfWebRequest $request)
    {
        $activityId = $request->getParameter('activityId');
        $sectionName = $request->getParameter('txtSectionName');
        $sectionParent = $request->getParameter('sbSectionParent');

        $item = new ActivityExtendedStatisticSections();
        $item->setArray(array('header' => $sectionName,
            'parent_id' => $sectionParent,
            'activity_id' => $activityId,
            'status' => 1));
        $item->save();

        $this->outputSections();
    }

    //Begin edit section
    function executeBeginEditSection(sfWebRequest $request)
    {
        $this->section = ActivityExtendedStatisticSectionsTable::getInstance()->find($request->getParameter('id'));

        $this->outputSections($request->getParameter('id'));
    }

    //Edit section
    function executeEditSection(sfWebRequest $request)
    {
        $sectionName = $request->getParameter('txtSectionName');
        $sectionParent = $request->getParameter('sbSectionParent');

        $item = ActivityExtendedStatisticSectionsTable::getInstance()->find($request->getParameter('id'));
        if ($item) {
            $item->setArray(array('header' => $sectionName,
                'parent_id' => $sectionParent));
            $item->save();
        }

        $this->outputSections();
    }

    //Delete section
    function executeDeleteSection(sfWebRequest $request)
    {
        ActivityExtendedStatisticSectionsTable::getInstance()->find($request->getParameter('id'))->delete();

        $this->outputSections();
    }

    //Sections list
    function executeSectionsList(sfWebRequest $request)
    {

    }

    //Add new field
    function executeAddField(sfWebRequest $request)
    {
        $field = new ActivityExtendedStatisticFields();

        $calcFields = $request->getParameter('calcFields');

        $field->setArray(array('header' => $request->getParameter('txtFieldName'),
            'description' => $request->getParameter('txtFieldDescription'),
            'parent_id' => $request->getParameter('sbFieldParent'),
            'activity_id' => $request->getParameter('activityId'),
            'value_type' => $request->getParameter('sbFieldType'),
            'status' => 1));
        $field->save();

        if (!empty($calcFields) && is_array($calcFields)) {
            foreach ($calcFields as $calcField) {
                $itemCalcField = new ActivityExtendedStatisticFieldsCalculated();

                $itemCalcField->setArray(array(
                    'parent_field' => $field->getId(),
                    'calc_field' => $calcField,
                    'calc_type' => $request->getParameter('sbCalcFieldsAction')));

                $itemCalcField->save();
            }
        }

        $this->outputSections(null, $request->getParameter('activityId'));
        $this->outputFields(null, $request->getParameter('activityId'));
    }

    //Begin add field
    function executeBeginEditField(sfWebRequest $request)
    {

    }

    //Edit field
    function executeEditField(sfWebRequest $request)
    {

    }

    //Delete field
    function executeDeleteField(sfWebRequest $request)
    {
        $field = ActivityExtendedStatisticFieldsTable::getInstance()->find($request->getParameter('id'));
        $activity = null;

        if ($field) {
            ActivityExtendedStatisticFieldsCalculatedTable::getInstance()->createQuery()->where('parent_field = ?', $field)->delete()->execute();

            $activity = $field->getActivityId();
            $field->delete();
        }

        $this->outputSections(null, $activity);
        $this->outputFields(null, $activity);
    }

    //Fields list
    function executeFieldsList(sfWebRequest $request)
    {

    }

    function outputSections($id = null, $parentId = null)
    {
        $query = ActivityExtendedStatisticSectionsTable::getInstance()->createQuery()->orderBy('id DESC');

        if (!empty($id))
            $query->andWhere('id != ?', $id);

        if (!empty($parentId))
            $query->andWhere('activity_id = ?', $parentId);

        $this->sections = $query->execute();
    }

    function outputFields($id = null, $parentId = null)
    {
        $query = ActivityExtendedStatisticFieldsTable::getInstance()->createQuery()->orderBy('id DESC');

        if (!empty($id))
            $query->andWhere('id != ?', $id);

        if (!empty($parentId))
            $query->andWhere('activity_id = ?', $parentId);

        $this->fields = $query->execute();
    }

    function outputCalculatedFields(sfWebRequest $request)
    {
        $ids = $request->getParameter('ids');
        $act = $request->getParameter('act');

        $result = array('fields' => array(), 'act' => $act);
        foreach ($ids as $id) {
            $field = ActivityExtendedStatisticFieldsTable::getInstance()->find($id);

            if ($field)
                $result['fields'][] = $field;
        }

        $this->fields = $result;
    }

    function outputCertificatedData($parentId)
    {
        $this->certificateItems = AgreementModelUserSettingsTable::getInstance()->createQuery()->where('activity_id = ?', $parentId)->execute();
    }

    function executeChangeCertificateDate(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $days = $request->getParameter('days');

        $item = AgreementModelUserSettingsTable::getInstance()->find($id);
        if ($item) {
            $toDate = date('Y-m-d', strtotime('+' . $days . ' days', strtotime(date('Y-m-d'))));

            $item->setCertificateEnd($toDate);
            $item->save();
        }

        $this->item = $item;
    }

    function executeAddDealerToMailList(sfWebRequest $request)
    {
        $item = new ActivityDealerMails();

        $item->setDealerId($request->getParameter('id'));
        $item->setActivityId($request->getParameter('activity'));
        $item->setDateTo(date('d-m-Y'));

        $item->save();

        $this->items = ActivityDealerMailsTable::getInstance()->createQuery()->where('activity_id = ?', $request->getParameter('activity'))->orderBy('id DESC')->execute();
    }

    function executeRemoveDealerFromMailList(sfWebRequest $request)
    {
        ActivityDealerMailsTable::getInstance()->find($request->getParameter('id'))->delete();

        return sfView::NONE;
    }

    function executeSendDealersMail(sfWebRequest $request)
    {
        $dealers = $request->getParameter('dealers');

        $dealersIds = array();
        if (!empty($dealers)) {
            $dealers = explode(',', $dealers);

            foreach ($dealers as $id) {
                $item = ActivityDealerMailsTable::getInstance()->createQuery()->select()
                    ->where('id = ? and activity_id = ?', array($id, $request->getParameter('activity')))
                    ->fetchOne();
                if ($item)
                    $dealersIds[] = $item;
            }
        } else {
            $dealers = ActivityDealerMailsTable::getInstance()->createQuery()->select()->orderBy('id ASC')->execute();
            foreach ($dealers as $key => $item)
                $dealersIds[] = $item;
        }

        $msgTemplate = $request->getParameter('msg');
        $totalSended = 0;

        foreach ($dealersIds as $item) {
            $msgText = $msgTemplate;

            if (strrpos($msgText, self::MAIL_TEMPLATE_DEALER_NAME) !== false) {
                $msgText = str_replace(self::MAIL_TEMPLATE_DEALER_NAME, $item->getDealer()->getName(), $msgText);
            }

            if (strrpos($msgText, self::MAIL_TEMPLATE_DEALER_DATE) !== false) {
                $msgText = str_replace(self::MAIL_TEMPLATE_DEALER_DATE, $item->getDateTo(), $msgText);
            }

            if (strrpos($msgText, self::MAIL_TEMPLATE_DEALER_DATE_MONTH) !== false) {
                $msgText = str_replace(self::MAIL_TEMPLATE_DEALER_DATE_MONTH, date("m", strtotime($item->getDateTo())), $msgText);
            }

            $userDealers = DealerUserTable::getInstance()
                ->createQuery()
                ->where('dealer_id = ?',
                    array($item->getDealer()->getId()))
                ->orderBy('id DESC')
                ->execute();

            foreach ($userDealers as $userDealer) {
                $user = $userDealer->getUser();

                if ($user->getAllowReceiveMails()) {
                    $mail = new ActivityDealersSendMail($user, $msgText);
                    if ($mail)
                        $mail->setPriority(1);

                    sfContext::getInstance()->getMailer()->send($mail);
                    $totalSended++;
                }
            }

        }

        if ($totalSended) {
            $sendItem = new ActivityDealerMailsSends();
            $sendItem->setMsg($msgTemplate);
            $sendItem->setActivityId($request->getParameter('activity'));
            $sendItem->save();
        }

        $this->sendMailTemplate = ActivityDealerMailsSendsTable::getInstance()->createQuery()->orderBy('id DESC')->limit(5)->execute();
    }

    public function executeChangeDealerMailDate(sfWebRequest $request)
    {
        $item = ActivityDealerMailsTable::getInstance()->find($request->getParameter('id'));
        if ($item) {
            $item->setDateTo($request->getParameter('data'));
            $item->save();
        }

        return sfView::NONE;
    }

    public function executeChangeFieldRequiredStatus(sfWebRequest $request)
    {
        $fieldId = $request->getParameter('fieldId');

        $field = ActivityExtendedStatisticFieldsTable::getInstance()->find($fieldId);
        if ($field) {
            $field->setRequired($request->getParameter('status'));
            $field->save();
        }

        return sfView::NONE;
    }

    public function executeConceptAdd(sfWebRequest $request)
    {
        $concept = $request->getParameter('concept');
        $dealerId = $request->getParameter('dealer_id');
        $activityId = $request->getParameter('activity_id');

        if (ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->where('dealer_id = ? and concept_id = ?', array($dealerId, $concept))->count() == 0) {
            $datas = ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->where('dealer_id = ?', $dealerId)->execute();
            foreach ($datas as $data) {
                $data->setConceptId($concept);
                $data->save();
            }
        }

        $this->dealerId = $dealerId;
        $this->activityId = $activityId;
        $this->dealerConcepts = ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->where('dealer_id = ?', $dealerId)->groupBy('concept_id')->execute();
    }

    public function executeDeleteConcept(sfWebRequest $request)
    {
        $concept = $request->getParameter('concept');

        $items = ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->where('concept_id = ?', $concept)->execute();
        foreach ($items as $item) {
            $item->setConceptId(0);
            $item->save();
        }

        return sfView::NONE;
    }

    public function executeExportExtendedStatisticToExcel(sfWebRequest $request)
    {
        $url = ActivityExtendedStatisticsBuilder::makeExportFile($request);
        echo $url;

        return sfView::NONE;
    }
}
