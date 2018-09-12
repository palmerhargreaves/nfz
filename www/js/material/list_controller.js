MaterialsListController = function(config) {
  // configurable {
  this.list_selector = ''; // required list selector
  this.win = null; // required a material window
  // }
  $.extend(this, config);
}

MaterialsListController.prototype = {
  start: function() {
    this.initEvents();
    this.checkHash();
    
    return this;
  },
  
  initEvents: function() {
    this.getListEl().on('click', '.banner', $.proxy(this.onClickPreview, this));
  },
  
  checkHash: function() {
    var matches = location.hash.match(/#material\/[0-9]+\/([0-9]+)/);
    if(matches) 
      this.loadMaterial(matches[1]);
  },
  
  loadMaterial: function(id) {
    this.win.load(id);
    this.markAsRead(id);
  },

  markAsRead: function(id) {
    $('.banner-' + id, this.getListEl()).addClass('closed');
  },
  
  getListEl: function() {
    return $(this.list_selector);
  },
  
  onClickPreview: function(e) {
    this.loadMaterial($(e.target).closest('.banner').data('material'));
  }
}