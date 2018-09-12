(function($) {
  $.current_id = 0;
  $.fn.generateId = function(prefix) {
    if(prefix === undefined)
      prefix = 'userid_';
      
    return this.each(function() {
      var el = $(this);
      if(!el.attr('id')) {
        var id = prefix + ($.current_id++);
        el.attr('id', id);
      }
    })
  }
  
  $.fn.getIdSelector = function(prefix) {
    if(this.length == 0)
      return undefined;
    
    this.generateId(prefix);
    return '#' + this.eq(0).attr('id');
  }
})(jQuery);
