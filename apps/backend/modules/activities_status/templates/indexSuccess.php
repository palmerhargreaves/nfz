
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span12">
      <div class="well sidebar-nav">
        <div class="alert alert-error container-error" style="display: none;"></div>

        <ul class="nav nav-list">
          <li class="nav-header">Выберите активность</li>
          <li>
            <select id='sbActivity' name='sbActivity'>
              <option value='-1'>Выберите активность ...</option>
              <?php foreach($activities as $act): ?>
                <option value='<?php echo $act->getId(); ?>'><?php echo sprintf('[%s] - %s', $act->getId(), $act->getName()); ?></option>
              <?php endforeach; ?> 
            </select>
          </li>
          
          <li class="nav-header">Выберите дилера</li>
          <li>
            <select id='sbDealer' name='sbDealer'>
              <option value='-1'>Выберите дилера ...</option>
              <?php foreach($dealers as $dealer): ?>
                <option value='<?php echo $dealer->getId(); ?>'><?php echo sprintf('[%s] - %s', $dealer->getNumber(), $dealer->getName()); ?></option>
              <?php endforeach; ?> 
            </select>
          </li>

          <li>
            <div class='btn-action-container'></div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="row-fluid">
    <div class="span12">
      <div class="well sidebar-nav">
		<div id="activities-blocked-list" class="row-fluid">
		<?php include_partial('blocked_list', array('items' => $blockedItems)); ?>
		</div>
	   </div>
	 </div>
	</div>
</div>

<script>
	$(function() {
		$(document).on('change', '#sbActivity, #sbDealer', function() {
			sendData('<?php echo url_for('activity_check_status'); ?>', null, true);
		});

		$(document).on('click', '.btn-block-activity', function() {
			//if(confirm('Заблокировать активность ?')) 
			{
				sendData('<?php echo url_for('activity_block_unblock'); ?>', null, false);
			}
		});

		$(document).on('click', '.btn-unblock-activity', function() {
			//if(confirm('Разблокировать активность ?')) 
			{
				sendData('<?php echo url_for('activity_block_unblock'); ?>', { id: $(this).data('id') }, false);	
			}
		});

		var sendData = function(url, data, show) {

			var activity = $('#sbActivity').val(),
					dealer = $('#sbDealer').val(),
					sendData = {};

			if(data != undefined)
				sendData = data;
			else {
				sendData.activity = activity;
				sendData.dealer = dealer;

				if(activity == -1 || dealer == -1)
					return;
			}

			$.post(url, sendData, function(result) {
				if(show)
					$('.btn-action-container').empty().html(result);
				else {
					$('#sbActivity, #sbDealer').val('-1');
					$('.btn-block-activity, .btn-unblock-activity').hide();
				}

				reloadBlockedList();
			});
		}

		var reloadBlockedList = function() {
			$.post('<?php echo url_for('activities_blocked_list'); ?>', function(result) {
				$('#activities-blocked-list').empty().html(result);
			});
		}
	});
</script>