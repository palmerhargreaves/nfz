<?php 
	if($data) {
		include_partial('service_action_modal_data', array('data' => $data, 'cls' => 'service-action-modal-contaner')); 
	}
	else
		echo "Нет данных";
?>