<div class="container-fluid">
  <div class="row-fluid">
    <div class="span12">
      <div class="well sidebar-nav">
        <div class="alert alert-error container-error" style="display: none;"></div>
        <div class="alert alert-success container-success" style="display: none;"></div>

        <ul class="nav nav-list">
          <li class="nav-header">Выберите активность для настройки расширенной статистики</li>
          <li>
            <select id='sbActivity' name='sbActivity'>
              <option value='-1'>Выберите активность ...</option>
              <?php foreach($activities as $act): ?>
                <option value='<?php echo $act->getId(); ?>'><?php echo sprintf('[%s] - %s', $act->getId(), $act->getName()); ?></option>
              <?php endforeach; ?> 
            </select>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div id="activity-statistic-data" class="row-fluid" style="display: none;"></div><!--/row-->
</div>

<script type="text/javascript">
  new ActivityStatisticBackend(
    {
      on_load_activity_data_url : '<?php echo url_for('@activity_extended_statistic_load_data'); ?>', //load data
      add_section_url: '<?php echo url_for('@activity_extended_statistic_add_section'); ?>', // add new section
      begin_edit_section_url: '<?php echo url_for('@activity_extended_statistic_begin_edit_section'); ?>', // edit section
      edit_section_url: '<?php echo url_for('@activity_extended_statistic_edit_section'); ?>', // edit section
      delete_section_url: '<?php echo url_for('@activity_extended_statistic_delete_section'); ?>', // delete section
      list_sections_url: '<?php echo url_for('@activity_extended_statistic_sections_list'); ?>', // sections list

      add_field_url: '<?php echo url_for('@activity_extended_statistic_add_field'); ?>', // add new field
      begin_edit_field_url: '<?php echo url_for('@activity_extended_statistic_begin_edit_field'); ?>', // edit section
      edit_field_url: '<?php echo url_for('@activity_extended_statistic_edit_field'); ?>', // edit field
      delete_field_url: '<?php echo url_for('@activity_extended_statistic_delete_field'); ?>', // delete field
      list_field_url: '<?php echo url_for('@activity_extended_statistic_fields_list'); ?>', // fields list
      change_field_required_status_url: '<?php echo url_for('@activity_extended_statistic_change_field_required_status'); ?>',

      add_dealer_to_mail_list: '<?php echo url_for('@activity_extended_add_dealer_to_mail_list'); ?>',
      remove_dealer_from_mail_list: '<?php echo url_for('@activity_extended_remove_dealer_from_mail_list'); ?>',
      send_dealers_mail: '<?php echo url_for('@activity_extended_send_dealers_mail'); ?>',
      on_change_dealer_mail_date_url: '<?php echo url_for('@activity_extended_change_dealer_mail_date'); ?>'
    }
  ).start();

	$('#reports-form input.date').datepicker({ dateFormat: "dd.mm.y" });
</script>