<div id="service-action-modal-success" class="intro modal" style="width: 250px;">
    <div class="modal-header">Сервисная акция!</div>
    <div class="modal-close modal-close-success"></div>
    <div class="modal-text">
    	<p><?php if($data) echo $data->getRawValue()->getSuccessMsg(); ?></p>
    </div>
        
</div>