<table class="table table-hover table-condensed table-bordered table-striped">
    <thead>
        <tr>
          <th style='width: 1%;'>#</th>
          <th>Дилер</th>
          <th>Дата действия сертификата до</th>
          <th>Продлено на (дней)</th>
          <th>Продлить на (дней)</th>
          <th style='width: 150px;'></th>
          <th></th>
        </tr>
    </thead>

    <tbody>
    <?php 
        $ind = 1;
        foreach($items as $item):
    ?>
        <tr class="item-container<?php echo $item->getId(); ?>">
            <?php include_partial('certificate_item_data', array('item' => $item)); ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
$(function(){
    $(document).on('input', '.input-days-field', function(){
        var regEx = new RegExp(/^[0-9.]+$/);
        if(!regEx.test($(this).val())) {
            //$(this).popmessage('show', 'error', 'Только числа');
            $(this).val($(this).val().replace(/[^\d]/, ''));
        }
        
        if(parseInt($(this).val()) != 0 && $(this).val().length != 0) 
            $('.input-button' + $(this).data('id')).fadeIn();
        else
            $('.input-button' + $(this).data('id')).fadeOut();
    });
    
    $(document).on('click', '.button-change-days', function() {
        var id = $(this).data('id'),
                days = $('.input-days' + id).val(),
                $el = $(this);
                
        if(confirm('Продлить на ' + $('.input-days' + id).val() + ' дн(я, ей) ?')) {
            $.post('<?php echo url_for('activity_change_certificate_date'); ?>', { id : id, days : days }, function(result) {
                $('tr.item-container' + id).empty().html(result);
            });
        }
            
    });
});
</script>
    
