(function($) {
  $.krikmodal = {
    shown: null,
    root: null
  }
  $.fn.krikmodal = function(cmd) {
    $(this).each(function() {
      
      if(cmd == 'show') {

        closeModal(true);
        $(this).fadeIn();
        
        var position = $(this).data('position') || 'auto';
        
        if(position != 'fixed') {
            $('body').append($(this));
            var top = $(document).scrollTop() + 15, height = $(this).height(), pHeight = $(window).height(),
                        width = $(this).width(), pWidth = $(window).width(),
                        left = (pWidth / 2) - (width / 2);
            
            top = $(document).scrollTop() + (pHeight / 2) - (height / 2) - 50; 
            top = Math.max(top, 65);

            $(this).offset( { top: top } );
            $(this).offset( { left: left } );
        }          
        
        getCloseButton(this).on('click', function() {
          closeModal();
        });
        $.krikmodal.shown = $(this);
        $.krikmodal.shown.trigger('show-modal');
      } else if(cmd == 'hide') {
        closeModal();
      }
    });
  };
  
  function getCloseButton(context) {
    return $('.modal-close', context);
  }
  
  function closeModal(skip_root) {
    if(!$.krikmodal.shown)
      return;
    
    $.krikmodal.shown.hide();
    getCloseButton($.krikmodal.shown).off('click', closeModal);
    $.krikmodal.shown.trigger('close-modal');
    $.krikmodal.shown = null;
    
    if(!skip_root && $.krikmodal.root)
      $($.krikmodal.root).krikmodal('show');
  }
  
  $(function() {
    $('.modal-trigger').click(function() {
      var selector = $(this).data('modal');
      if(!selector) 
        return;

      $(selector).krikmodal('show');

      return false;
    });    
  });
})(jQuery);
