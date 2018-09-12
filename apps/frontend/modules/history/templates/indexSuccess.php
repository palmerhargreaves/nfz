<a href="<?php echo url_for('home/index') ?>" class="small back button">Назад</a>
<div style="margin-top: 24px" class="content-header">История событий</div>
<div style="top: 40px;" class="content-search">			
    <form action="<?php echo url_for('@history_page') ?>" method="get" id="history-search-form">
        <input type="text" name="search" id="search">
        <input type="submit" id="search-button" value="">
    </form>
</div>

<div id="history">
  <div class="history-wrapper">
  <?php include_partial('list', array('history' => $history)) ?>
    <div id="history-load-place"></div>			
  </div>
  <div class="preloader" id="history-preloader"></div>			
</div>

<script type="text/javascript">
$(function() {
  new AutoPagerSearcher({
    search_form: "#history-search-form",
    pager: new AutoPager({
      markerSelector: '#history-preloader',
      placeHolder: '#history-load-place',
      listUrl: "<?php echo url_for('@history_page') ?>",
      pageLen: <?php echo $page_len ?>
    }).start()
  }).start();
  
  $('#history .history-wrapper').on('click', '.history-item', function() {
    var $a = $('a', this);
    if($a.length > 0)
      location.href = $a.attr('href');
      
    return false;
  });
});
</script>