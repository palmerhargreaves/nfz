      <div class="approvement">
          <h1>Пользователи</h1>
                      
          <div id="accommodation" class="active">
          <div id="agreement-models">
          
          <div id="filters" style='width: 800px;'>
            <form action="<?php echo url_for('@agreement_module_specialist_users') ?>" method="get">
             
             <?php
              	$dealer_roles = array('-1' => 'Все', '2' => 'Импортёры', '3' => 'Дилеры');

              ?>
              <div class="modal-select-wrapper krik-select select type filter">
                  <span class="select-value"><?php echo $dealer_roles[$sf_data->getRaw('dealer_role')] ?></span>
                  <div class="ico"></div>
                  <input type="hidden" name="dealer_role" value="<?php echo $dealer_role ?>">
                  <div class="modal-input-error-icon error-icon"></div>
                  <div class="error message"></div>
                  <div class="modal-select-dropdown">
					<?php foreach($dealer_roles as $value => $name): ?>
                      <div class="modal-select-dropdown-item select-item" data-value="<?php echo $value ?>"><?php echo $name ?></div>
					<?php endforeach; ?>
                  </div>
              </div>

              <div class="date-input filter">
                  <input type="text" placeholder="номер" name="dealer_number" value="<?php echo $dealer_number ?>" />
              </div>

              <div class="post-input filter">
                  <input type="text" placeholder="должность" name="dealer_post" value="<?php echo $dealer_post ?>" />
              </div>
              
            </form>

             <form id="exportToExcel" action="<?php echo url_for('@agreement_module_specialist_users_excel') ?>" method="get">
                <div style="float: right; margin: 5px;"><input type="button" class="button small" value="Выгрузить"></div>
              </form>
          </div>

<?php if(count($users) > 0): ?>
              <h2>Список пользователей (дилеры, импортеры)</h2>

              <form action="<?php echo url_for('@agreement_module_specialist_users') ?>" method="get">
              <table class="models" id="users-list">
                  <thead>
                      <tr>
                          <td width="100"><div class="has-sort">Номер дилера</div></td>
                          <td width="100"><div class="has-sort">Название дилера</div></td>
                          <td width="100"><div class="has-sort">Группа</div><div class="sort has-sort"></div></td>
                          <td width="130"><div class="has-sort">Email</div><div class="sort has-sort"></div></td>
                          <!--<td width="146"><div>Период</div></td>-->
                          <td width="40">Имя</td>
                          <td width="40"><div>Фамилия</div></td>
                          <td width="140"><div>Должность</div></td>
                          <td width=""><div>Активен</div></td>
                          <td></td>
                          <td width="150"></td>
                      </tr>
                  </thead>
                  <tbody>

  <?php foreach($users as $user): 
  			$group = $user->getGroup();
  			$roles = array(2, 3);

  			if(!in_array($group->getId(), $roles))
  				continue;

  			$dealer = $user->getDealerUsers()->getFirst();
  			if(empty($dealer))
  				continue;

  			$dealer = $dealer->getDealer();

  			//(count($dealers));
  ?>
                      <tr class="sorted-row model-row<?php if($n % 2 == 0) echo ' even' ?>" >
                          <td data-sort-value="<?php echo $dealer->getNumber(); ?>"><?php echo sprintf('%s', substr($dealer->getNumber(), 5)); ?></td>
                          <td data-sort-value="<?php echo $dealer->getName(); ?>"><div style="max-width:100px;word-wrap:break-word;"><?php echo sprintf('%s', $dealer->getName()); ?></div></td>

                          <td data-sort-value="<?php echo $user->getGroup()->getName() ?>"><?php echo $user->getGroup()->getName() ?></td>
                          <td data-sort-value="<?php echo $user->getEmail() ?>"><div style="max-width:100px;word-wrap:break-word;"><?php echo $user->getEmail() ?></div><div class="sort"></div></td>
                          <td ><?php echo $user->getName() ?></td>
                          <td data-sort-value="<?php echo $user->getSurname() ?>"><div><?php echo $user->getSurname() ?></div><div class="sort"></div></td>
                          <td data-sort-value="<?php echo $user->getPost() ?>"><div style="max-width:80px;word-wrap:break-word;font-size:11px;"><?php echo $user->getPost() ?></div><div class="sort"></div></td>
                          <td ><div class="<?php echo $user->getActive() ? "ok" : "pencil" ?>"></div></td>
                          <td >
                          	<?php 
                          		if(!$user->getActive()):
                          	?>
                          		<input type="button" class="button small" style="padding-left:5px;padding-right:5px;" value="Активировать" data-user-id="<?php echo $user->getId(); ?>">
                          	<?php endif; ?>
                          </td>
                          <td><div class="contributor" style="margin:0 -15px;" data-user-id='<?php echo $user->getId(); ?>'><div class='remove' style='display: block; float:none;margin: 0 auto; padding:0;position:relative;right:0;top:0;'></div></div>&nbsp;</td>
                      </tr>
  <?php endforeach; ?>
                  </tbody>
              </table>

              <input name="dealer_id_to_activate" type="hidden" value="-1">
              </form>
<?php endif; ?>
          </div>
        
        </div>
      </div>

<script type="text/javascript">
$(function() {
  new TableSorter({
    selector: '#users-list'
  }).start();
  
  $('#filters form :input[name]').change(function() {
    this.form.submit();
  });
  
  $('#filters form .with-date').datepicker();

  $("#users-list input[type=button]").live('click', function(){
  	if(confirm('Активировать пользователя ?')) {
	  	$(this).closest("form").find("input[name=dealer_id_to_activate]").val($(this).data('user-id'));
		$(this).closest("form").submit();  	
	}
  });

  $("#exportToExcel input[type=button]").live('click', function() {
    $(this).closest("form").submit();
  });

  $('.contributor ').live('click', function() {
    if(confirm('Удалить пользователя ?')) {
      var $from = $(this);

      $.post("<?php echo url_for('@agreement_module_specialist_users_delete'); ?>",
            {
              userId : $from.data('user-id')
            },
            function(result) {
              if(result.success) {
                alert('Пользователь успешно удален.');
                $from.closest('tr').hide();  
              }

              
          });
      
    }
  });
});
</script>