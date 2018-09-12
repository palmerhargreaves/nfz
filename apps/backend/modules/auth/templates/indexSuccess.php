<form action="<?php echo url_for('auth/index') ?>" method="post">
<div class="modal show">
  <div class="modal-header">
  <h3><?php echo sfConfig::get('app_site_name'); ?></h3>
</div>
    <div class="modal-body">
      <table>
<?php echo $form->render() ?>
      </table>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Вход</button>
    </div>
</div>
</form>
