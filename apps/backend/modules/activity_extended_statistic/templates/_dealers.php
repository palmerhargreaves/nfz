<ul class="nav nav-list" style="width: 90%;">
  <li class="nav-header">Дилеры</li>

  <li>
  	<select id='sbMailDealers' name='sbMailDealers'>
	<?php foreach(DealerTable::getVwDealersQuery()->execute() as $dealer): ?>
   		<option value='<?php echo $dealer->getId(); ?>'><?php echo sprintf("[%s] %s", $dealer->getNumber(), $dealer->getName()); ?></option>
  	<?php endforeach; ?>
  	</select>
  	<img src='/images/plus-icon.png' class='add-mail-dealer' 
  							data-id='<?php echo $dealer->getId(); ?>' 
  							data-name='<?php echo $dealer->getName(); ?>' 
  							data-number='<?php echo $dealer->getNumber(); ?>' 
  							title='Добавить дилера' style='float: right; margin-top: 8px; cursor: pointer;' />
  </li>
</ul>

<div class='container-sended-mails-to-dealers'>
<?php include_partial("mail_templates", array("templates" => ActivityDealerMailsSendsTable::getInstance()->createQuery()->orderBy('id DESC')->limit(5)->execute())); ?>
</div>
