<?php include_partial('auth/form') ?>
<?php include_partial('recovery_password/form') ?>
<?php include_partial('registration/form', array('form' => new RegistrationForm())) ?>

<script type="text/javascript">
$('#forgot-pass-link').click(function () {
  $('#recovery-form input[name=email]').val($('#auth-form input[name=login]').val());
});  
</script>
