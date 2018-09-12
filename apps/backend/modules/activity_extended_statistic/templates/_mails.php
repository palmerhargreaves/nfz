<ul class="nav nav-list" style="margin-bottom: 25px;">
  <li class="nav-header">Текст рассылки</li>
  <li><textarea id='txtMailMsg' name='txtMailMsg' rows='7' style='width: 700px;'></textarea></li>
  <li style='font-size: 12px; padding-left: 10px; padding-bottom: 10px;'>Доступные параметры: {dealer_name} - название дилера, {date} - дата, {date_month} - дата (месяц)</li>
  <li><input type='button' id='btSendDealersMail' class='btn btn-normal' value='Разослать' /></li>
</ul>

<ul class="nav nav-list">
  <li class="nav-header">Список дилеров для рассылки</li>
</ul>

<?php include_partial('mail_dealers_list', array('items' => $items));