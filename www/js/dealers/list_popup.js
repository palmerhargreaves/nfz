DealersListPopup = function(config) {
  // configurable {
  this.handler_selector = ''; // required
  this.popup_selector = ''; // required
  // }
  $.extend(this, config);
}

DealersListPopup.prototype = {
  start: function() {
    this.initEvents();
    
    return this;
  },
    
  initEvents: function() {
    this.getHandlers().click($.proxy(this.onClickHandler, this));
  },
  
  loadContent: function(url) {
    this.showPopup();
    this.getPopupTextBlock()
        .empty().html('<img src="/images/form-loader.gif" alt="загружается..."/>')
        .load(url, { rand: Math.random() });
    
  },
  
  showPopup: function() {
    this.getPopup().krikmodal('show');
  },
  
  hidePopup: function() {
    this.getPopup().hide();
  },
  
  getHandlers: function() {
    return $(this.handler_selector);
  },
  
  getPopupTextBlock: function() {
    return $('.modal-text', this.getPopup());
  },
  
  getPopup: function() {
    return $(this.popup_selector);
  },
  
  onClickHandler: function(e) {
    var url = $(e.target).closest(this.handler_selector).data('url');
    
    this.loadContent(url);
  }
}