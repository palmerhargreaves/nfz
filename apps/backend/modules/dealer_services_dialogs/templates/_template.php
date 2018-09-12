<?php
  $template = DealersServicesDialogTemplatesTable::getInstance()->find($dealer_services_dialogs->getTemplate());
  if($template)
    echo $template->getHeader();
  