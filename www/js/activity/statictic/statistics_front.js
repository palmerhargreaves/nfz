TasksAcceptor = function(config) {
  // configurable {
  this.modal = ''; // required selector of a dealers select
  this.tasks_url = ''; // required url to load dealer tasks
  this.loader_image = '/images/action-loader.gif';
  // }
  $.extend(this, config);
  
  this.activity_id = 0;
}

TasksAcceptor.prototype = {
  start: function() {
    this.initEvents();
    
    return this;
  },
  
  initEvents: function() {
    this.getDealersSelect().change($.proxy(this.onSelectDealer, this));
    this.getTasksListBlock().on('click', 'a', $.proxy(this.onClickActionLink, this));
  },
  
  showWithActivity: function(activity_id, name) {
    this.activity_id = activity_id;
    this.getActivityNameBlock().html(name);
    
    this.getModal().modal('show');
    this.loadTasks();
  },
  
  loadTasks: function() {
    this.getTasksListBlock().empty();
    
    if(!this.getDealerId())
      return;
    
    $.post(this.tasks_url, {
      activity_id: this.activity_id,
      dealer_id: this.getDealerId()
    }, $.proxy(this.onLoadTasksList, this));
  },
  
  executeAction: function($a) {
    $a.html('<img src="' + this.loader_image + '" alt=""/>');
    
    $.post($a.attr('href'), $.proxy(this.onActionResult, this));
  },

  getDealerId: function() {
    return this.getDealersSelect().val();
  },
  
  getDealersSelect: function() {
    return $('select[name=dealer_id]', this.getModal());
  },
  
  getTasksListBlock: function() {
    return $('.tasks', this.getModal());
  },
  
  getActivityNameBlock: function() {
    return $('.activity-name', this.getModal());
  },
  
  getModal: function() {
    return $(this.modal);
  },
  
  onSelectDealer: function() {
    this.loadTasks();
  },
  
  onLoadTasksList: function(data) {
    this.getTasksListBlock().html(data);
  },
  
  onClickActionLink: function(e) {
    this.executeAction($(e.target).closest('a'));
    
    return false;
  },
  
  onActionResult: function() {
    this.loadTasks();
  }
}