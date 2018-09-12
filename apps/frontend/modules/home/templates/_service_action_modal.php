<div id="<?php echo $cls ? $cls : "service-action-modal"; ?>" class="intro modal" <?php if($data) { ?> style="width: <?php echo $data->getWidth(); ?>px;" <?php } ?>>
    <?php if($data) include_partial('service_action_modal_data', array('data' => $data, 'cls' => $cls)); ?>
</div>

<script>
    $(function() {
        $('#frmDate .with-date').datepicker();

        $('.model .accept-button').live('click', function() {
            $('#containerAcceptServiceAction').slideDown('fast');
        });

        /*$('#service-action-modal .model .decline-button').live('click', function() {
            $("#service-action-modal").krikmodal('hide');
        });*/

        $('#containerAcceptServiceAction .model button.accept-button2, .model .decline-button').live('click', function(e) {
            e.preventDefault();

            var startDate = '',
                endDate = '',
                actType = $(this).data('act-type'),
                valid = true;

            <?php if(!$data->getWithoutDates()): ?>
                var $startDate = $('input[name=startDate]'),
                        $endDate = $('input[name=endDate]');

                if(actType == 'accept') {
                    if($startDate.val().length == 0) {
                        $startDate.popmessage('show', 'error', 'Необходимо исправить период размещения');
                        valid = false;
                    }

                    if($endDate.val().length == 0) {
                        $endDate.popmessage('show', 'error', 'Необходимо исправить период размещения');
                        valid = false;
                    }

                    if(!valid)
                        return;

                    $startDate.popmessage('hide');
                    $endDate.popmessage('hide');

                    var t1 = parseDate($startDate.val()).getTime(),
                            t2 = parseDate($endDate.val()).getTime();

                    if(t1 >= t2) {
                        $endDate.popmessage('show', 'error', 'Конечная дата должна быть больше начальной');

                        return;
                    }
                }

                startDate = $startDate.val();
                endDate = $endDate.val();
            <?php endif; ?>

            var dialogId = $(this).data('dialog-id');

            $.post("/home/serviceAction", 
                    {
                        startDate : startDate,
                        endDate : endDate,
                        dialogId : dialogId,
                        actType: actType
                    },
                    function(result) {
                    	var dialogsChooseCount = $('.service-dialog-item').length;
                        
                        if(dialogsChooseCount == 0)
                        	$("#service-action-modal").krikmodal('hide'); 
                        else
                        	$("#service-action-modal-container").krikmodal('hide');

                        showChooseDialgos(dialogId, true);

                        if(actType == 'accept') {
                        	if(dialogsChooseCount > 0)
                            	$('#service-action-modal-success .modal-text').html(result.msg);

                            $("#service-action-modal-success").krikmodal('show');
                        }
                });
        });

		$('.modal-close-success').live('click', function() {
			$('#service-action-modal-success').hide();

			showChooseDialgos(-1, false);
		});

        $('.modal-close-many').live('click', function() {
            showChooseDialgos(-1, false);
        });

        var showChooseDialgos = function(dialogId, remove) {
            if(remove != undefined && remove)
                $('.service-dialog-' + dialogId).remove();

            if($('.service-dialog-item').length > 0) {
                $("#service-action-modal-container").krikmodal('hide');
                $("#service-action-choose-modal").krikmodal('show');                            
            }
        }

        $('button.decline-button2').live('click', function(e) {
            e.preventDefault();

            if($('.service-dialog-item').length == 0)
                $("#service-action-modal").krikmodal('hide'); 
            else 
                showChooseDialgos(-1, false);
        });

        var parseDate = function(date) {
            if(date != undefined) {
                var tmp = date.split('.').reverse();

                return new Date(tmp[0], tmp[1], tmp[2]);
            }

            return null;
        }
    });

</script>

