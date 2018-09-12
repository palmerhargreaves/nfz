<?php include_partial('modal_model') ?>
      <div class="approvement">
          <h1>Согласование</h1>
                      
          <div id="accommodation" class="active">
          <div id="agreement-models">
          <?php 
            include_partial('concepts', array(
              'concepts' => $concepts,
            )) 
          ?>

          <?php
            $wait_filter_items = array(
              'manager' => 'Менеджеры',
              'specialist' => 'Специалисты',
              'dealer' => 'Дилеры',
              'agreed' => 'Согласованные',
              'all' => 'Все',
            );
          ?>

          <!--<div id="filters">
            <form action="<?php echo url_for('@agreement_module_specialist_models') ?>" method="get">
             
              <div class="modal-select-wrapper krik-select select dealer filter">
<?php if($dealer_filter): ?>
                  <span class="select-value"><?php echo $dealer_filter->getRawValue() ?></span>
                  <input type="hidden" name="dealer_id" value="<?php echo $dealer_filter->getId() ?>">
<?php else: ?>
                  <span class="select-value">Все дилеры</span>
                  <input type="hidden" name="dealer_id">
<?php endif; ?>
                  <div class="ico"></div>
                  <span class="select-filter"><input type="text"></span>
                  <div class="modal-input-error-icon error-icon"></div>
                  <div class="error message"></div>
                  <div class="modal-select-dropdown">
                      <div class="modal-select-dropdown-item select-item" data-value="">Все</div>
<?php foreach($dealers as $dealer): ?>
                      <div class="modal-select-dropdown-item select-item" data-value="<?php echo $dealer->getId() ?>"><?php echo $dealer->getRawValue() ?></div>
<?php endforeach; ?>
                  </div>
              </div>
              <div class="date-input filter">
                  <input type="text" placeholder="от" name="start_date" value="<?php echo $start_date_filter ? date('d.m.Y', $start_date_filter) : '' ?>" class="with-date"/>
              </div>
              <div class="date-input filter">
                  <input type="text" placeholder="до" name="end_date" class="with-date" value="<?php echo $end_date_filter ? date('d.m.Y', $end_date_filter) : '' ?>"/>
              </div>
                <div class="date-input filter">
                  <input type="text" placeholder="№ заявки" name="model" value="<?php echo $model_filter ?>" />
              </div>
            </form>
          </div>-->

<?php if(count($models) > 0): ?>
              <h2>Макеты</h2>
              <table class="models" id="agreement-models">
                  <thead>
                      <tr>
                          <td width="75"><div class="has-sort">ID / Дата</div><div class="sort has-sort"></div></td>
                          <td width="146"><div class="has-sort">Дилер</div><div class="sort has-sort"></div></td>
                          <td width="180"><div class="has-sort">Название</div><div class="sort has-sort"></div></td>
                          <td width="30"><div></div></td>
                          <td width="146"><div>Размещение</div></td>
                          <!--<td width="146"><div>Период</div></td>-->
                          <td width="81"><div class="has-sort">Сумма</div><div class="sort has-sort" data-sort="cost"></div></td>
                          
                          <?php
                          	if($sf_user->isSpecialist() || $sf_user->isManager()) {
                          ?>
                          	<td><div>Согласуйте до</div></td>                            
                          <?php } else { ?>
                          	<td><div>Действие</div></td>
                          <?php } ?>

                          <td width="35"><div>Макет</div></td>
                          <td width="35"><div>Отчет</div></td>
                          <td width="35"><div><div class="has-sort">&nbsp;</div><!--div class="sort has-sort" data-sort="messages"></div--></div></td>
                      </tr>
                  </thead>
                  <tbody>
  <?php $k = 0; foreach($models as $n => $model):  ?>
    <?php $discussion = $model->getDiscussion() ?>
    <?php $new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0 ?>
                      <tr class="sorted-row model-row<?php if($k % 2 == 0) echo ' even' ?>" data-model="<?php echo $model->getId() ?>" data-discussion="<?php echo $model->getDiscussionId() ?>" data-new-messages="<?php echo $new_messages_count ?>">
                          <td data-sort-value="<?php echo $model->getId() ?>"><div class="num">№ <?php echo $model->getId() ?></div><div class="date"><?php echo D::toLongRus($model->created_at) ?></div></td>
                          <td data-sort-value="<?php echo $model->getDealer()->getName() ?>"><?php echo $model->getDealer()->getName(), ' (', $model->getDealer()->getNumber(), ')' ?></td>
                          <td data-sort-value="<?php echo $model->getName() ?>"><div><?php echo $model->getName() ?></div><div class="sort"></div></td>
                          <td style="padding: 5px; background-position: 3px center;" class="placement <?php echo $model->getModelType()->getIdentifier() ?>"></td>
                          <td class="placement"><div class="address"><?php echo $model->getValueByType('place') ?></div></td>
                          <!--<td><?php echo $model->getValueByType('period') ?></td>-->
                          <td data-sort-value="<?php echo $model->getCost() ?>"><div><?php echo number_format($model->getCost(), 0, '.', ' ') ?> руб.</div><div class="sort"></div></td>

                          <?php if(($sf_user->isSpecialist() || $sf_user->isManager())) {
                              if($model->getCssStatus() != 'ok') {
                           ?>
                            <td class="darker" style="<?php echo $model->isModelAcceptActiveToday($sf_user->isImporter() ? false : $sf_user->isDealerUser()) ? 'background-color: rgb(233, 66, 66);' : '' ?>">
                              <?php if($model->getCssStatus() != 'ok') { ?> <div><?php echo date('H:i d-m-Y', $n); ?></div><div class="sort"><?php } ?></div>
                            </td>
                          <?php } else { ?>
                            <td class="darker" style=""><div class="sort"></div></td>
                          <?php } ?>

                          <?php } else { ?>
                            <td class="darker"><div><?php echo $model->getSpecialistActionText() ?></div><div class="sort"></div></td>
                          <?php } ?>

                          <td class="darker"><div class="<?php echo $model->getCssStatus() ?>"><!--x10--></div></td>
                          <td class="darker"><div class="<?php echo $model->getReportCssStatus() ?>"></div></td>
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
      </div>

<script type="text/javascript">
$(function() {
  new TableSorter({
    selector: '#agreement-models'
  }).start();


  $('#filters form :input[name]').change(function() {
    this.form.submit();
  });
  
  $('#filters form .with-date').datepicker();
});

</script>

