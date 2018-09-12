<!DOCTYPE html>
<html>
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
<!--[if lte IE 8]>
<link rel="stylesheet" type="text/css" media="screen" href="/css/ie.css" />
<![endif]-->
  </head>
  <body class="<?php if (has_slot('body-class')): ?><?php include_slot('body-class') ?><?php else: ?>anonymous<?php endif; ?>">
    <div id="site">
      <?php include_partial('global/head') ?>
      
      <div id="content">
      <?php echo $sf_content ?>
      </div>
      
      <?php include_partial('global/footer') ?>
    </div>
  </body>
</html>
