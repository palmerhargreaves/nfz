<div class="activity">
<?php 
	include_partial('activity/activity_head', array('activity' => $activity));
?>
	<div class="content-wrapper">
	  <?php include_partial('activity/activity_tabs', array('activity' => $activity, 'active' => 'statistic')) ?>

	 	
		<div class="pane-shadow"></div>
		        <div class="pane clear">
		                <div id="statistic" class="active">
						<?php if($activity->getActivityField()->count() > 0): ?>
		                  
		                  <div class="main-column">
		                  	<div class="statistics contentContainer" style="width: 800px;">

		                       <div class='model'>
						            <form id='frmStatistics'>
						                <div style="display: block; width: 99%; margin: auto; margin-top: 10px;">
						                	<?php
						                		$descr = $activity->getStatsDescription();
						                		if(!empty($descr)) {
						                	?>
						                	<p style="margin-top: 5px;"><?php echo $descr; ?></p>
	
						                	<?php } ?>
						                	<p class="stats-description" style="color: red; display: none; margin-top: 25px;">
						                		В периоде начальная дата должна быть меньше (равна) даты окончания<br />
      											Все поля должны быть заполнены (для числовых значение разрешено использование "." )
      										</p>

						                    <table style='width: 100%; margin-top: 30px;' class="models">
						                        <tbody>
						                            <?php
						                                $n = 1;
						                                $fields = ActivityFieldsTable::getInstance()->createQuery()->select('*')->where('activity_id = ?', $activity->getId())->orderBy('id ASC')->execute();
						                                //foreach($activity->getActivityField() as $item):
						                                foreach($fields as $item):
                                                            $field = $item->getFieldByDealer($sf_user->getRawValue()->getAuthUser());

						                                	if(!$field) continue;
						                            ?>
						                            <tr class="model-mode-field sorted-row model-row<?php if($n++ % 2 == 0) echo ' even' ?>" >
						                                <td style="font-size: 12px;"><?php echo $item->getName(); ?></td>
						                                <td class="field controls" style="width: 235px;">
						                                    <?php
						                                    	if($item->getType() == "date") {
						                                    		$period = explode("-", $field->getVal());
						                                    ?>
							                                    <div class="modal-input-wrapper input" style='width: 75px; margin: 7px; float: left;' >
							                                     	<input type='text' name="periodStart" class='with-date' style='height: 31px;' placeholder='От' value="<?php echo $period[0]; ?>"
							                                     				data-type="<?php echo $item->getType(); ?>"
							                                     				data-regexp="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$" 
							                                     				data-field-id="<?php echo $field->getId(); ?>" required="true">
							                                        <div class="modal-input-error-icon error-icon"></div>
							                                        <div class="error message" style='display: none; z-index: 1;'></div>
							                                    </div>
							                                    <div class="modal-input-wrapper input" style='width: 75px; margin: 7px; float: right;' >
							                                     	<input type='text' name="periodEnd" class='with-date' style='height: 31px;' placeholder='До' value="<?php echo $period[1]; ?>"
							                                     				data-type="<?php echo $item->getType(); ?>"
							                                     				data-regexp="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$" 
							                                     				data-field-id="<?php echo $field->getId(); ?>" required="true">
							                                        <div class="modal-input-error-icon error-icon"></div>
							                                        <div class="error message" style='display: none; z-index: 1;'></div>
							                                    </div>
						                                    <?php } else { ?>
							                                    <div class="modal-input-wrapper input" style='width: 75px; margin: 7px; float: right;' >
							                                     	<input type='text' class='' placeholder='0' style='height: 31px;'
							                                     				data-type="<?php echo $item->getType(); ?>"
							                                     				<?php if($item->getType() == "number") { ?>
							                                     					data-regexp="/^[0-9.]+$/" 
							                                     				<?php } else { ?>
							                                     					data-regexp="/^[0-9a-zA-Zа-яА-Я\_\(\)\+\-\= ]+$/" 
							                                     				<?php } ?>
							                                     				data-field-id="<?php echo $field->getId(); ?>" required="true" value="<?php echo $field->getVal(); ?>">
							                                        <div class="modal-input-error-icon error-icon"></div>
							                                        <div class="error message" style='display: none; z-index: 1;'></div>
							                                    </div>
						                                    <?php } ?>
						                                </td>
						                            </tr>

						                            <?php endforeach; ?>
						                        </tbody>
						                    </table>

						                    <div class="info-save-complete" style="display: none; width: 99%; margin: 10px; padding: 10px; color: red; text-align: center; font-weight: bold;">
						                    	Параметры статистики успешно сохранены !
						                    </div>

						                    <button class="button apply-stat-button" style="width: 25%; float: right; margin: 10px;"
                                            							data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'>Сохранить</button>	
						                </div>
						            </form>
						        </div>

					        </div>
		                  
		                  <!--<div class="print button">Печать</div>-->
		                  
		              </div>
					 <?php endif; ?>

                    <div class="clear"></div>
                </div>
        </div>        
	</div>
</div>

<script>
	$(function(){
		$("input[type=text]").live("input", function() {
			var reg = new RegExp($(this).data('regexp'));

			if($(this).data('type') != 'date') {
				if(!reg.test($(this).val()) && $(this).data('type') == 'number')
					$(this).val($(this).val().replace(/[^\d.]/, ''));
			}

		});

		$(".apply-stat-button").click(function(e) {
			var hasError = false, data = [];

			e.preventDefault();
			$.each($("#frmStatistics input[type=text]"), function(ind, el){
				var regExp = new RegExp($(el).data('regexp'));

				$(el).parent().css('border-color', '');
				if($(el).attr("required") && $(el).val().length == 0) {
					$(el).parent().css('border-color', 'red');
					hasError = true;
				}
				else if($(el).data('type') == "date" && !regExp.test($(el).val()))
				{
					$(el).parent().css('border-color', 'red');
					hasError = true;
				}

				if($(el).data('type') != "date")
					data.push( {
						id : $(el).data('field-id'),
						value : $(el).val()
					});
			});

			var startDate = getElDate($('input[name*=Start]')),
					endDate = getElDate($('input[name*=End]'));

			if(startDate == undefined || endDate == undefined)
				return;

			if(endDate < startDate) {
				$('input[name*=Start]').parent().css('border-color', 'red');
				$('input[name*=End]').parent().css('border-color', 'red');

				hasError = true;
			}

			if(hasError) {
				$("#frmStatistics .stats-description").fadeIn();
				return;
			}

			data.push({
				id : $('input[name*=Start]').data('field-id'),
				value : $('input[name*=Start]').val() + '-' + $('input[name*=End]').val()
			});

			var bt = $(this);
			bt.fadeOut();

			$("#frmStatistics .stats-description").fadeOut();
			$.post("<?php echo url_for('@activity_change_stats'); ?>",
				{ data : data },
				function(result){
					$('.info-save-complete').fadeIn("slow");

					setTimeout(function() {
						$('.info-save-complete').fadeOut('fast');
						bt.fadeIn('normal');
					}, 3000);
				}
			);
		});

		var parseDate = function(date) {
		    if(date != undefined) {
		  	 var tmp = date.split('.').reverse();

		     return new Date(tmp[0], tmp[1] - 1, tmp[2]);
		    }

		    return null;
		}
		  
		var getElDate = function(el) {
		  	var tmp = '';
		  	
		  	tmp = $(el);
		  	if(tmp != undefined)
		  		return parseDate(tmp.val()).getTime();
		  	  
		  	return null;
		}

		$('#frmStatistics .with-date').datepicker();
	});


</script>