AutoPagerSearcher = function(config) {
  // configurable { 
  this.search_form = ''; // required selector of seacrh form
  this.pager = null; // required an auto pager
  // }
  $.extend(this, config);
}

AutoPagerSearcher.prototype = {
  start: function() {
    this.initEvents();
    
    return this;
  },
  
  initEvents: function() {
    this.getSearchForm().submit($.proxy(this.onSearch, this));
  },
  
  search: function() {
    this.pager.setParam('search', this.getSearchField().val());
    this.pager.reload();
  },
  
  getSearchField: function() {
    return $(':input[name=search]', this.getSearchForm());
  },
  
  getSearchForm: function() {
    return $(this.search_form);
  },
  
  onSearch: function() {
    this.search();
    
    return false;
  }
}