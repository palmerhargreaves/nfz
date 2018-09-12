(function($) {
  $.fn.krikselect = function() {
    $(this).each(function() {
      
      var $filter_block = $('.select-filter', this);
      var $filter_field = $(':input', $filter_block);
      var has_filter = $filter_block.length > 0;
      var $value_text = $('.select-value', this);
      var $value_field = $('input[type=hidden]', this);
      var filter_value = '';
      
      var select = this;
      
      $(this).click(function(e){
        if(!$(this).is('.inactive') && $(e.target).closest('.select-filter').length == 0) {
          $(this).toggleClass('open');
          
          if($(this).hasClass('open')) {
              $(this).find(".modal-select-dropdown").slideDown("fast");
              switch_to_filter();
          }
          else {
            $(this).find(".modal-select-dropdown").slideUp("fast");
            switch_to_value();
          }
        }
      });
      
      init_filter();
      
      $('.select-item', this).click(function() {
        if($(select).is('.inactive')) {
          $(select).find(".modal-select-dropdown").slideUp("fast");
          return;
        }
        
        var $item = $(this).closest('.select-item');
        var value = $item.data('value');
        if(value !== undefined) {
          $('.select-value', select).html($item.html());
          $value_field.val(value);
          $value_field.change();
        }
      });
      
      $value_field.bind('update', function() {
        var option = $(':input', select).val();
        var text = ''
        $('.select-item', select).each(function() {
          if($(this).data('value') == option) {
            text = $(this) .html();
          }
        });
        $('.select-value', select).html(text);
      });
      
      function init_filter() {
        if(!has_filter)
          return;
        
        setInterval(apply_filter, 100);
      }
      
      function apply_filter() {
        if(filter_value == $filter_field.val())
          return;

        
        filter_value = $filter_field.val();
        if(filter_value) {
          var filter_re = new RegExp(escapeRegExp(filter_value), 'i');
          $('.select-item', select)
            .hide()
            .filter(function() {
              return filter_re.test($(this).html());
            }).show();
        } else {
          $('.select-item', select).show();
        }
      }
      
      function switch_to_filter() {
        if(!has_filter)
          return;

        $value_text.hide();
        $filter_block.show();
        $filter_field.focus().val('');
      }
      
      function switch_to_value() {
        if(!has_filter)
          return;
        
        $value_text.show();
        $filter_block.hide();
      }
      
      function escapeRegExp(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
      }
      
    });
  }
  
  $(function() {
    $('.krik-select').krikselect();
  });
})(jQuery);