<?php include_partial('modal_model', array('decline_reasons' => $decline_reasons, 'decline_report_reasons' => $decline_report_reasons, 'specialist_groups' => $specialist_groups, 'outOfDate' => $outOfDate)) ?>
<?php include_partial('menu', array('active' => 'agreement')) ?>
      <div class="approvement">
          <h1>Мои заявки</h1>
<?php
$status_filter = array(
  'all' => 'Все',
  'in_work' => 'На проверке',
  'complete' => 'Завершенные',
  'process_draft' => 'Черновик',
  'process_reports' => 'Отчеты'
);
?>
          <div id="filters" style='left: 175px;'>
            <form action="<?php echo url_for('@agreement_module_management_activities') ?>" method="get">
              <div class="modal-select-wrapper krik-select select type filter">
                  <span class="select-value"><?php echo $status_filter[$sf_data->getRaw('activity_status')] ?></span>
                  <div class="ico"></div>
                  <input type="hidden" name="activity_status" value="<?php echo $activity_status ?>">
                  <div class="modal-input-error-icon error-icon"></div>
                  <div class="error message"></div>
                  <div class="modal-select-dropdown">
<?php foreach($status_filter as $value => $name): ?>
                      <div class="modal-select-dropdown-item select-item" data-value="<?php echo $value ?>"><?php echo $name ?></div>
<?php endforeach; ?>
                  </div>
              </div>
              <div class="date-input filter">
                  <input type="text" placeholder="от" name="start_date" value="<?php echo $start_date_filter ? date('d.m.Y', $start_date_filter) : '' ?>" class="with-date"/>
              </div>
              <div class="date-input filter">
                  <input type="text" placeholder="до" name="end_date" class="with-date" value="<?php echo $end_date_filter ? date('d.m.Y', $end_date_filter) : '' ?>"/>
              </div>
              

            </form>
          </div>
          <br/>
          
          <div id="agreement-models">
                      
<?php if(count($models) > 0): ?>
              <h2>Мои заявки</h2>
              <table class="models" id="models-list">
                  <thead>
                      <tr>
                          <td width="75"><div class="has-sort">ID / Дата</div><div class="sort has-sort"></div></td>
                          <td width="146"><div class="has-sort">Дилер</div><div class="sort has-sort"></div></td>
                          <td width="180"><div class="has-sort">Название</div><div class="sort has-sort"></div></td>
                          <!--<td width="146"><div>Размещение</div></td>-->
                          <td width="105"><div>Период</div></td>
                          <td width="81"><div class="has-sort">Сумма</div><div class="sort has-sort" data-sort="cost"></div></td>
                          <td><div>Действие</div></td>
<?php if($activity_status && ($activity_status == 'in_work' || $activity_status == 'all')) { ?>
                          <td width="100"><div>На проверке до</div></td>
<?php } else if($activity_status && $activity_status == 'process_reports') { ?>
						  <td width="100"><div>Загрузить до</div></td>
<?php } ?>

                          <td width="35"><div>Макет</div></td>
                          <td width="35"><div>Отчет</div></td>
                          <td width="35"><div><div class="has-sort">&nbsp;</div><!--div class="sort has-sort" data-sort="messages"></div--></div></td>
                      </tr>
                  </thead>
                  <tbody>

  <?php $k = 0; foreach($models as $n => $model): ?>
    <?php $discussion = $model->getDiscussion() ?>
    <?php $new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0 ?>
                      <tr  class="sorted-row model-row<?php if($k % 2 == 0) echo ' even' ?>" data-model="<?php echo $model->getId() ?>" data-discussion="<?php echo $model->getDiscussionId() ?>" data-new-messages="<?php echo $new_messages_count ?>">
                          <td data-sort-value="<?php echo $model->getId() ?>"><div class="num">№ <?php echo $model->getId() ?></div><div class="date"><?php echo D::toLongRus($model->created_at) ?></div></td>
                          <td data-sort-value="<?php echo $model->getDealer()->getName() ?>"><?php echo $model->getDealer()->getName(), ' (', $model->getDealer()->getNumber(), ')' ?></td>
                          <td data-sort-value="<?php echo $model->getName() ?>"><div><?php echo $model->getName() ?></div><div class="sort"></div></td>
                          <?php /*<td class="placement <?php echo $model->getModelType()->getIdentifier() ?>"><div class="address"><?php echo $model->getValueByType('place') ?></div></td> */?>
                          <td><?php echo $model->getValueByType('period') ?></td>
                          <td data-sort-value="<?php echo $model->getCost() ?>"><div><?php echo number_format($model->getCost(), 0, '.', ' ') ?> руб.</div><div class="sort"></div></td>
                          <td class="darker"><div><?php echo $model->getDealerActionText() ?></div><div class="sort"></div></td>
                          
                          <?php if($activity_status && $activity_status == 'in_work') { ?>
                          	<?php if($model->getCssStatus() != 'ok') { ?>
                            	<td class="darker" style="<?php echo $model->isModelAcceptActiveToday($sf_user->isDealerUser()) ? 'background-color: rgb(233, 66, 66);' : '' ?>"><div><?php echo date('H:i d-m-Y', $n); ?></div><div class="sort"></div></td>
                            <?php } else { ?>
                            	<td class="darker"><div class="sort"></div></td>
                            <?php } ?>
                          <?php }  else if($activity_status && $activity_status == 'all') { 
                          		if($model->getCssStatus() == 'clock') {
                          	?>
                          		<td class="darker" style="<?php echo $model->isModelAcceptActiveToday($sf_user->isDealerUser()) ? 'background-color: rgb(233, 66, 66);' : '' ?>"><div><?php echo date('H:i d-m-Y', $n); ?></div><div class="sort"></div></td>	
                          	<?php } else { ?>
                          		<td class="darker"><div class="sort"></div></td>	
                          	<?php } ?>
                          <?php } else if($activity_status && $activity_status == 'process_reports') { ?>
                          		<td class="darker" style="<?php echo $model->isModelAcceptActiveToday($sf_user->isDealerUser()) ? 'background-color: rgb(233, 66, 66);' : '' ?>"><div><?php echo date('H:i d-m-Y', $n); ?></div><div class="sort"></div></td>	
                          <?php } ?>

                          <?php $waiting_specialists = $model->countWaitingSpecialists(); ?>
                          <td class="darker"><div class="<?php echo $model->getCssStatus() ?>"><?php echo $waiting_specialists ? 'x'.$waiting_specialists : '' ?></div></td>
                          <?php $waiting_specialists = $model->countReportWaitingSpecialists(); ?>
                          <td class="darker"><div class="<?php echo $model->getReportCssStatus() ?>"><?php echo $waiting_specialists ? 'x'.$waiting_specialists : '' ?></div></td>
                          <td data-sort-value="<?php echo $new_messages_count ?>" class="darker">
  <?php if($new_messages_count > 0): ?>
                            <div class="message"><?php echo $new_messages_count ?></div>
  <?php endif; ?>
                          </td>
                      </tr>
  <?php $k++; endforeach; ?>
                  </tbody>
              </table>
<?php endif; ?>
          </div>
        </div>

<script type="text/javascript">
$(function() {
  new TableSorter({
    selector: '#models-list'
  }).start();
  
  $('#filters form :input[name]').change(function() {
    this.form.submit();
  });
  
  $('#filters form .with-date').datepicker();
});
</script>
