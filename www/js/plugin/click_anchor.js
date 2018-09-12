(function($) {
  $.fn.clickAnchor = function() {
    $(this).each(function() {
      var $a = $(this);
      var $form = $('<form>').attr('action', $a.attr('href')).appendTo('body');
      if($a.attr('target'))
        $form.attr('target', $a.attr('target'));
      
      $form.submit();
    })
  }
})(jQuery)