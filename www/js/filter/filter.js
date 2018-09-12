Filter = function(config) {
  // configurable {
  this.field = ''; // required selector of a field for filtration
  this.filtering_blocks = '' ; // required selector of blocks to filter
  this.groups_selector = '.filter-group';
  this.filter_delay = 100;
  // }
  $.extend(this, config);
  
  this.filter_timer = null;
  this.value = '';
}

Filter.prototype = {
  start: function() {
    this._initTimer();
    
    return this;
  },
  
  filter: function() {
    if(this.value == this.getField().val())
      return;
    
    this.value = this.getField().val();
    
    if(this.value) {
      
      this._hideAllFilteringBlocks();
      
      var filter_re = new RegExp(this.escapeRegExp(this.value), 'i');
      this.getFilteringBlocks().filter(function() {
        return filter_re.test($(this).data('filter'));
      }).show();
      
    } else {
      
      this._showAllFilteringBlocks();
      
    }
  },
  
  _hideAllFilteringBlocks: function() {
    this.getFilteringBlocks().hide().prev(this.groups_selector).hide();
  },
  
  _showAllFilteringBlocks: function() {
    this.getFilteringBlocks().show().prev(this.groups_selector).show();
  },
  
  _initTimer: function() {
    this._clearTimer();
    
    this.filter_timer = setTimeout($.proxy(this.onTimeToFilter, this), this.filter_delay);
  },
  
  _clearTimer: function() {
    if(this.filter_timer)
      clearTimeout(this.filter_timer);
    
    this.filter_timer = null;
  },
  
  escapeRegExp: function(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
  },
  
  getFilteringBlocks: function() {
    return $(this.filtering_blocks);
  },
  
  getField: function() {
    return $(this.field);
  },
  
  onTimeToFilter: function() {
    this.filter();
    
    this._initTimer();
  }
}