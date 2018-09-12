<?php

/**
 * Description of AgreementDealerModelBlockInform
 *
 */
class AgreementDealerModelBlockInform extends TemplatedMail
{
  function __construct(User $user, AgreementModel $model, $msgType = 'week')
  {
    $site_url = sfConfig::get('app_site_url');
    
    $link = "<a href='".$site_url."/activity/".$model->getActivityId()."/module/agreement/models/model/".$model->getId()."'>ссылка</a>";

    if($msgType == 'unblock')
      $text = $this->getUnblockMsg($model, $link);  
    else
      $text = ($msgType == 'week') ? $this->getWeekText($model, $link) : $this->getDayText($model, $link);
    
    parent::__construct(
        $user->getEmail(), 
        'global/mail_common', 
        array(
          'user' => $user,
          'subject' => $msgType == 'unblock' ? 'Разблокирование заявки' : 'Блокирование заявки',
          'text' => $text
      )
    );
  }

  protected function getWeekText($model, $link) {
    $values = $model->getValues();

    $value = '';
    $ids = array(31, 30, 29, 4, 7, 11, 15, 19, 25);
    foreach($values as $v) {
      if(in_array($v->getFieldId(), $ids))
          $value = $v->getValue();
    }

    $today = date("d-m-Y");
    if(empty($value))
      $value = $this->getCreatedAt();
    else {
      $value = explode('-', $value);
      if(!empty($value[1])) {
        $value = $value[1];

        $value = explode('.', $value);
        $value = sprintf('%s-%s-20%s', $value[0], $value[1], $value[2]);
      }
    }

    $days = 10;
    $plusDays = 0;

    for($i = 1; $i <= $days; $i++ ) {
      $tempDate = date("d-m-Y", strtotime('+'.$i.' days', D::toUnix($value)));
      $d = getdate(strtotime($tempDate));

      if($d['wday'] == 0 || $d['wday'] == 6)
        $plusDays++;

      $plusDays += $model->checkDateInCalendar($tempDate);
    }

    $days += $plusDays;

    $value = date('d-m-Y', strtotime('+'.$days.' days', D::toUnix($value)));

    return
<<<TEXT
        <p>Внимание! Вам необходимо загрузить отчетные документы по заявке №{$model->getId()} "{$model->getName()}" в срок до {$value}:</p>
<p>фотоотчет</p>
<p>финансовые документы (счет, акт выполненных работ или товарная накладная).</p>
<p>В противном случае данная заявка будет заблокирована.</p>
<p>Перейти к заявке ({$link})</p>
        </p>
TEXT;
  }

  protected function getDayText($model, $link) {
    return
<<<TEXT
        <p>Внимание! Заявка № {$model->getId()} "{$model->getName()}" заблокирована.</p>
        <p>Перейти к заявке ({$link})</p>
TEXT;
  }

  protected function getUnblockMsg(AgreementModel $model, $link) {
     return
<<<TEXT
        Уважаемый дилер!
        Заявка № {$model->getId()} разблокирована.
        Вам необходимо подгрузить отчет в течение 2 рабочих дней.
        В противном случае данная заявка будет повторно заблокирована.
        <p>Перейти к заявке ({$link})</p>
TEXT;
  }
}
