<div id="service-action-choose-modal" class="intro modal">
    <div class="modal-header">Выберите сервисную акцию!</div>
    <div class="modal-close"></div>
    <div class="modal-text">
        <div style="display: block; margin-left: 30px: margin-top: 1px;">
        <?php foreach($data as $item): ?>
            <div class='service-dialog-item service-dialog-<?php echo $item->getId(); ?>'>
                <a href='javascript:;' class='show-service-modal-dialog' data-id='<?php echo $item->getId(); ?>'> <?php echo $item->getHeader(); ?></a>
                <span style='font-size: 11px; font-weight: normal; margin: 5px; float:left; '><?php echo Utils::trim_text($item->getRawValue()->getDescription(), 255); ?></span>
            </div>
        <?php endforeach; ?>            
        </div>
    </div>    
  
</div>

<script>
    $(function() {
        $('.show-service-modal-dialog').live('click', function() {
            var el = $(this);

            $.post('<?php echo url_for('service_dialogs_show'); ?>', { id : $(this).data('id') }, function(result) {
                $('#service-action-modal-container').empty().html(result);
                $('#service-action-modal-container').krikmodal('show');
                
                //el.parent().remove();
            });
        });
    });

</script>

