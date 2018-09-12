<?php include_partial('modal_model', array('decline_reasons' => $decline_reasons, 'decline_report_reasons' => $decline_report_reasons, 'specialist_groups' => $specialist_groups)) ?>
<?php include_partial('menu', array('active' => 'agreement')) ?>
      <div class="approvement">
          <h1>Согласование</h1>
<?php
$wait_filter_items = array(
  'manager' => 'Менеджеры',
  'specialist' => 'Специалисты',
  'dealer' => 'Черновики',
  'agreed' => 'Согласованные',
  'all' => 'Все',
);

$model_status_filter_items = array (
  'all' => 'Все',
  'accepted' => 'Согласованы',
  'wait' => 'Не обработаны',
  'comment' => 'Отклонены',
  );

if($sf_user->isAdmin() || $sf_user->isImporter())
  $model_status_filter_items['blocked'] = 'Заблокированные';

?>
          <div id="filters">
            <form action="<?php echo url_for('@agreement_module_management_models') ?>" method="get">
              <div class="modal-select-wrapper krik-select select type filter">
                  <span class="select-value"><?php echo $wait_filter_items[$sf_data->getRaw('wait_filter')] ?></span>
                  <div class="ico"></div>
                  <input type="hidden" name="wait" value="<?php echo $wait_filter ?>">
                  <div class="modal-input-error-icon error-icon"></div>
                  <div class="error message"></div>
                  <div class="modal-select-dropdown">
<?php foreach($wait_filter_items as $value => $name): ?>
                      <div class="modal-select-dropdown-item select-item" data-value="<?php echo $value ?>"><?php echo $name ?></div>
<?php endforeach; ?>
                  </div>
              </div>
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

<?php if($sf_user->isManager()) { ?>
              <div class="modal-select-wrapper krik-select select dealer filter" style="margin-left: 0px;">
                  <?php if($designer_filter): ?>
                  <span class="select-value"><?php echo $designer_filter->getRawValue() ?></span>
                  <input type="hidden" name="designer_id" value="<?php echo $designer_filter->getId() ?>">
<?php else: ?>
                  <span class="select-value">Без дизайнера</span>
                  <input type="hidden" name="designer_id">
<?php endif; ?>

                  <div class="ico"></div>
                  <span class="select-filter"><input type="text"></span>
                  <div class="modal-input-error-icon error-icon"></div>
                  <div class="error message"></div>
                  <div class="modal-select-dropdown">
                    <div class="modal-select-dropdown-item select-item" data-value="">Без дизайнера</div>
<?php foreach($designers as $designer): ?>
                      <div class="modal-select-dropdown-item select-item" data-value="<?php echo $designer->getId() ?>"><?php echo sprintf('%s %s (%s)', $designer->getRawValue(), $designer->getSurname(), $designer->getPost()); ?></div>
<?php endforeach; ?>
                  </div>
              </div>

              <div class="modal-select-wrapper krik-select select type filter" style="margin-left: 10px;">
                  <span class="select-value"><?php echo $model_status_filter_items[$sf_data->getRaw('model_status_filter')] ?></span>
                  <div class="ico"></div>
                  
                  <input type="hidden" name="model_status" value="<?php echo $model_status_filter ?>">
                  
                  <div class="modal-input-error-icon error-icon"></div>
                  <div class="error message"></div>
                  <div class="modal-select-dropdown">
<?php foreach($model_status_filter_items as $value => $name): ?>
                      <div class="modal-select-dropdown-item select-item" data-value="<?php echo $value ?>"><?php echo $name ?></div>
<?php endforeach; ?>
                  </div>
              </div>
<?php } ?>

<?php
  $model_type_filter_items = array('all' => 'Все', 'makets' => 'Согласование макетов', 'reports' => 'Согласование отчетов');
?>
            <div class="modal-select-wrapper krik-select select type filter" style="margin-left: 10px; width: 200px;">
                  <span class="select-value"><?php echo $model_type_filter_items[$sf_data->getRaw('model_type_filter')] ?></span>
                  <div class="ico"></div>
                  
                  <input type="hidden" name="model_type" value="<?php echo $model_type_filter ?>">
                  
                  <div class="modal-input-error-icon error-icon"></div>
                  <div class="error message"></div>
                  <div class="modal-select-dropdown">
<?php foreach($model_type_filter_items as $value => $name): ?>
                      <div class="modal-select-dropdown-item select-item" data-value="<?php echo $value ?>" ><?php echo $name ?></div>
<?php endforeach; ?>
                  </div>
              </div>


            </form>
          </div>

          <br/>
          
          <div id="agreement-models" data-url='<?php echo url_for('@agreement_module_management_model_unblock'); ?>'>
          <?php 
            if(!$designer_filter && !($sf_data->getRaw('model_status_filter') == 'blocked'))
              include_partial('concepts', array(
                'wait_filter' => $wait_filter,
                'concepts' => $concepts,
              ));
          ?>
                      
<?php if(count($models) > 0): ?>
              <h2>Макеты</h2>
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
<?php if($sf_user->isManager()) { ?>
                          <td width="100"><div>Согласовать до</div></td>
<?php } ?>

                          <td width="35"><div>Макет</div></td>
                          <td width="35"><div>Отчет</div></td>
                          <td width="35"><div><div class="has-sort">&nbsp;</div><!--div class="sort has-sort" data-sort="messages"></div--></div></td>
                      </tr>
                  </thead>
                  <tbody>
                  <?php 
                    include_partial('models_items', array('models' => $models, 
                                                            'wait_filter' => $wait_filter, 
                                                            'model_status_filter' => $model_status_filter ));
                  ?>    
                  </tbody>
              </table>

<?php endif; ?>
          </div>
        </div>

<script type="text/javascript">
  var isLoading = false;

  $(function() {
    new TableSorter({
      selector: '#models-list'
  }).start();
  
  $('#filters form :input[name]').change(function() {
    this.form.submit();
  });
  
  $('#filters form .with-date').datepicker();

  window.addEventListener('scroll', function(event) {
    if(isLoading)
      return;

    if((this.scrollY * 100 / document.body.clientHeight) > 60) {
      isLoading = true;

      $("#models-list tbody").append("<tr><td colspan='7' style='text-align: center;'><img src='/images/action-loader.gif' /></td></tr>");

      $.post("<?php echo url_for('agreement_model_load_models'); ?>", function(result) {
        $("#models-list tbody tr:last").remove();
        $("#models-list tbody").append(result);
        
        isLoading = false;
      });
    }

  });
});
</script>

