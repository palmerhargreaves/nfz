<div style="display: block; width: 100%;">
	<div style="display: block; float: left; width: 25%; margin: 5px;">
		<h2>Кварталы</h2>

		<div style="display: block; margin: auto; width: 99%;">
			<form id="frmQtDays" method="post" action="<?php echo url_for('calendar_budget_change_days'); ?>">
				<p><strong>Список годов / кварталов (дни):</strong></p>
				
				<select name='sb_year' id='sb_year' data-url='<?php echo url_for('calendar_budget_change_year'); ?>'>
				<?php
					foreach($years as $year) {
						echo '<option value="'.$year->getYear().'">'.$year->getYear().'</option>';
					}
				?>
				</select>
				
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Квартал</th>
							<th>Начало квартала</th>
						</tr>
					</thead>

					<tbody>
					<?php 
						foreach($quarters as $item):
					?>
						<tr>
							<td><?php echo sprintf("Квартал %d", $item->getQuarter()); ?></td>
							<td><input type="text" name="qtDays[]" class="input-small input-qt-days quarter-<?php echo $item->getQuarter(); ?>" value="<?php echo $item->getDay(); ?>" data-id="<?php echo $item->getQuarter(); ?>"  required></td>
						</tr>
					<?php endforeach; ?>

						<tr>
							<td colspan="2"><input id="btSaveQtDays" type="button" class="btn" value="Сохранить" style="float: right; margin-right: 15px;"></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>

	<div style="display: block; float: left; width: 73%; margin: 5px;">
		<h2>Календарь</h2>

		<div style="display: block; margin: auto; width: 99%;">
			<form id="frmCalendarDays" method="post" action="">
				<p><strong>Добавление нового события (праздника):</strong></p>
				<input type="text" name="title" placeholder="Заголовок" value="" class="input" required> 
				<input type="text" name="start_date" placeholder="от" value="<?php echo isset($start_date) ? $start_date : '' ?>" class="input-small date" required>
				<input type="text" name="end_date" placeholder="до" value="<?php echo isset($end_date) ? $end_date : '' ?>" class="input-small date">
				<input type="submit" value="Добавить" class="btn" data-url='<?php echo url_for('calendar_add_date'); ?>'>

				<div id="calendar" class="well"></div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript">
	$(function(){
		$("#btSaveQtDays").click(function(e) {
			var hasError = false, days = [], $bt = $(this), regEx = new RegExp(/^[0-9.]+$/);;

			e.preventDefault();
			$.each($("#frmQtDays input[type=text]"), function(ind, el) {
				if($(el).attr("required")) {
					if($(el).val().length == 0) {
						$(el).css( "border-color" , "red" );
						hasError = true;
					}
					else {

						if(!regEx.test($(el).val())) {
			                //$(this).popmessage('show', 'error', 'Только числа');
			                $(el).val($(el).val().replace(/[^\d]/, ''));
			            }

						days.push( { id : $(el).data('id'), day : $(el).val() } );
					}
				}
            
			});

			if(hasError)
				return;

			$bt.fadeOut();
			$.post($("#frmQtDays").attr('action'),
					{
						data : days,
						year : $('#sb_year').val()
					},
					function(result) {
						$bt.fadeIn()
					}
			);
			//$("#frmQtDays").submit();
		});

		$(document).on('click', '#sb_year', function() {
			var $el = $(this);

			$.post($el.data('url'), 
						{ year : $el.val() },
					function(result) {
						$.each(result.result, function(ind, val) {
							$('.quarter-' + ind).val(val);
						});
					});
		});

		/*$(document).on("input", $(".input-qt-days"), function(el) {
			var regEx = new RegExp(/^[0-9.]+$/);

            if(!regEx.test($(el.target).val())) {
                //$(this).popmessage('show', 'error', 'Только числа');
                $(el.target).val($(el.target).val().replace(/[^\d]/, ''));
            }
		});*/

		$('input.date').datepicker({ dateFormat: "dd.mm.yy" });

		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next',
				center: 'title',
				right: 'month'
			},
			editable: true,
			lang : 'ru',
			nextDayThreshold: "01:00:00",
			eventClick : function(event) {
				$.post("<?php echo url_for(calendar_remove_date); ?>",
					{
						start_date : event.start._i
					},
					function() {
						//$('#calendar').fullCalendar("removeEvents", (event._id));
						$('#calendar').fullCalendar("removeEvents", function(ev) {
							return event._id == ev._id;
						});

						$('#calendar').fullCalendar("rerenderEvents");  
					}
				);
			},
			eventSources: [
				{ 
					url : "<?php echo url_for('calendar_load_dates'); ?>",
					ignoreTimeZone : true
				}
			],
			eventDrop : function(event, delta, revertFunc) {
				if(!confirm('Изменить дату события ?')) 
					revertFunc();
				else {
					var oldStart = event.start._i,
						newStart = event.start.format(),
							end = '';

					if(event.end)
						end = event.end.format();

					$.post("<?php echo url_for('calendar_change_date'); ?>",
						{
							old_start_date : oldStart,
							new_start_date : newStart,
							end_date : end
						}, 
						function(result) {

						}
					);
				}
			}
		});

		$("input[type=submit]").click(function(e) {
			e.preventDefault();

			var title = $("input[name=title]").val(),
					start_date = $("input[name=start_date]").val(),
					end_date = $("input[name=end_date]").val(),
					hasError = false,
					url = $(this).data('url');

			$.each($("input[type=text]"), function(ind, el) {
				if($(el).attr('required') && $.trim($(el).val()).length == 0) {
					$(el).css( 'border-color', 'red' );
					hasError = true;
				}
				else
					$(el).css( 'border-color', '' );
			});

			if(hasError) {
				return;
			}

			start_date = formatDate(start_date);
			end_date = formatDate(end_date); 

			
			var evObj = {
				title : title,
				allDay : true,
				start : start_date,
				end : addDay(end_date),
				//id : 1
			};

			$("#frmCalendarDays input[type=text]").val('');
			$("#calendar").fullCalendar("renderEvent", evObj);

			$.post(url,
				{
					title : title,
					start_date : start_date,
					end_date : end_date
				},
				function(result) {
					
				}
			);

		});


		var formatDate = function(date) {
			if(date.length == 0)
				return '';

			var tDate = date.split('.');
			return tDate[2] + '-' + tDate[1] + '-' + tDate[0];
		}

		var addDay = function(date, days) {
			var d = new Date(date);

			d.setDate(d.getDate() + 1);
			return d;
		}
	});
	
</script>