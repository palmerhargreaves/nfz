<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
</head>
<body>
<?php if ($sf_user->isAuthenticated()): ?>
    <?php include_partial('global/navigation') ?>
<?php endif; ?>

<?php echo $sf_content ?>

<script>
    $(function() {
        window.activity_formulas_fields = new FormulasFields({
            param1_selector: '#activity_efficiency_formula_param_param1_type',
            param2_selector: '#activity_efficiency_formula_param_param2_type',
            on_load_param_data_url: '<?php echo url_for('@activity_efficiency_load_param_data'); ?>'
        }).start();
    });
</script>


</body>
</html>
