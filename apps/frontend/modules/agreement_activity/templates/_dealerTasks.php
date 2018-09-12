<div class="stage<?php if($descriptor->isFirstTaskReady($dealer->getRawValue())) echo ' active' ?>">Согласование рекламных материалов</div>
<div class="stage<?php if($descriptor->isSecondTaskReady($dealer->getRawValue())) echo ' active' ?>">Отчет о размещении рекламных материалов</div>
<div class="stage last<?php if($descriptor->isThirdTaskReady($dealer->getRawValue())) echo ' active' ?>">Отчет по результатам акции</div>
