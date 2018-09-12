ConfirmDelete = function(config) {
  // configurable {
  this.modal = ''; // required modal selector
  // }
  $.extend(this, config);
  
  this.del_link = '';
}

ConfirmDelete.prototype = {
  start: function() {
    this.initEvents();
    
    return this;
  },
  
  initEvents: function() {
    this.getDeleteButton().click($.proxy(this.onDelete, this));
  },
  
  confirm: function(link, title) {
    this.del_link = link;
    
    this.getTitle().html(title);
    this.getModal().krikmodal('show');
  },
  
  getTitle: function() {
    return $('.title', this.getModal());
  },
  
  getDeleteButton: function() {
    return $('.delete', this.getModal());
  },
  
  getModal: function() {
    return $(this.modal);
  },
  
  onDelete: function() {
    location.href = this.del_link;
  }
}


window.confirm_delete = $.proxy(ConfirmDelete.prototype.confirm, new ConfirmDelete({
  modal: '#confirm-delete'
}).start());
