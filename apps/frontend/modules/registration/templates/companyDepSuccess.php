<?php
  $defValues = array(1 => 'Руководитель отдела сервиса', 2 => 'Менеджер по маркетингу и рекламе', 3 => 'Менеджер ОЗЧ');
?>
<div class="modal-select-wrapper select krik-select company-post-krik-select">
	<span class="select-value"><?php echo $companyDep != 4 ? $defValues[$companyDep] : "Генеральный директор"; ?></span>
	<div class="ico"></div>
	<input type="hidden" name="post" value="<?php echo $companyDep == 4 ? 'Генеральный директор' : $defValues[$companyDep]; ?>">

  <div class="modal-select-dropdown">
	<?php if($companyDep == 1): ?>
      <div class="modal-select-dropdown-item select-item" data-value="Руководитель отдела сервиса">Руководитель отдела сервиса</div>
      <div class="modal-select-dropdown-item select-item" data-value="Технический директор">Технический директор</div>
    <?php elseif($companyDep == 2): ?>
      <div class="modal-select-dropdown-item select-item" data-value="Менеджер по маркетингу и рекламе">Менеджер по маркетингу и рекламе</div>
      <div class="modal-select-dropdown-item select-item" data-value="Руководитель отдела маркетинга">Руководитель отдела маркетинга</div>
      <div class="modal-select-dropdown-item select-item" data-value="Бренд-менеджер">Бренд-менеджер</div>
      <div class="modal-select-dropdown-item select-item" data-value="Ведущий специалист отдела маркетинга">Ведущий специалист отдела маркетинга</div>
	<?php elseif($companyDep == 3): ?>
      <div class="modal-select-dropdown-item select-item" data-value="Менеджер ОЗЧ">Менеджер ОЗЧ</div>
      <div class="modal-select-dropdown-item select-item" data-value="Менеджер по продажам допоборудования">Менеджер по продажам допоборудования</div>
      <div class="modal-select-dropdown-item select-item" data-value="Руководитель отдела ОЗЧ">Руководитель отдела ОЗЧ</div>
    <?php else: ?>
      <div class="modal-select-dropdown-item select-item" data-value="Генеральный директор">Генеральный директор</div>
    <?php endif; ?>
	</div>
  
</div>