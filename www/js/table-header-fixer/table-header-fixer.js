TableHeaderFixer = function(config) {
  // configurable {
  this.selector = null; // required selector of a table
  this.update_time = 500;
  // }
  $.extend(this, config);
  
  this.clone = null;
}

TableHeaderFixer.prototype = {
  start: function() {
    this.initTimer();
    this.initEvents();
    
    return this;
  },
  
  initEvents: function() {
    $(window).scroll($.proxy(this.onUpdateClonePosition, this));
  },
  
  initTimer: function() {
    setInterval($.proxy(this.onUpdateClonePosition, this), this.update_time);
  },
  
  _createClone: function() {
    var $clone = $('<table/>');
    
    $clone.attr('class', this.getTable().attr('class'))
          .addClass('clone')
          .css('z-index', 100);
          
    $clone.append(this.getHeader().clone());
    $clone.appendTo('body');
    
    return $clone.getIdSelector();
  },
  
  getClone: function() {
    if(!this.clone) {
      this.clone = this._createClone();
    }
    return $(this.clone);
  },
  
  getHeader: function() {
    return $('thead', this.getTable());
  },
  
  getTable: function() {
    return $(this.selector);
  },
  
  onUpdateClonePosition: function() {
    var table_offset = this.getTable().offset();
    
    if($(window).scrollTop() > this.getTable().offset().top) {
      
      this.getClone().css({
        position: 'fixed',
        left: table_offset.left,
        top: 0
      });
      
    } else {
      
      this.getClone().css({
        position: 'absolute',
        left: table_offset.left,
        top: table_offset.top
      });
      
    }
  }
}