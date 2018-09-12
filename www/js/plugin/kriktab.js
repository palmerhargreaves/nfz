(function($) {
  $.fn.kriktab = function(cmd) {
    var args = arguments;
    
    $(this).each(function() {
      if(cmd == 'activate') {
        activateTab($(args[1]), $(this));
      } else if(cmd === undefined) {
        var $tabs = $(this);
        $('.tab', $tabs).each(function() {
          var $tab = $(this);
          if($tab.data('pane')) {
            $tab.addClass('has-pane').click(function() {
              if($(this).closest('.disabled').length == 0)
                activateTab($(this), $tabs);
            });
          }
        });
        
        var $current = $('.has-pane.active', $tabs).eq(0);
        if($current.length == 0)
          $current = $('.has-pane', $tabs).eq(0);
          
        activateTab($current, $tabs);
      }
      
    });
  }
  
  function activateTab($tab, $tabs) {
    $('.tab', $tabs).each(function() {
      var $tab = $(this);
      var pane_id = $tab.data('pane');
      if(pane_id) {
        $tab.removeClass('active');
        $('#' + pane_id).hide();
      }
    });
    $tab.addClass('active').trigger('activated', [ $tab, $tabs ]);
    $('#' + $tab.data('pane')).show();
  }
  
  $(function() {
    $('.tabs').kriktab();
  });
})(jQuery)