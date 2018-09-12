ns('utils');

/**
 * Observable
 * 
 * @version $Id: observable.js 1188 2010-07-23 15:25:17Z  $
 */
utils.Observable = function(config) {
  this.listeners = {};
  if(!this.events)
    this.events = {};
  
  if(config)
    $.extend(this, config);
    
  this.addListeners(this.listeners);
}

utils.Observable.prototype = {
  
  addEvents: function(events) {
    this.events = $.extend(this.events || {}, events);
  },
  
  addListeners: function(listeners) {
    var _this = this;
    var scope = listeners.scope ? listeners.scope : window;
    $.each(listeners, function(name, handler) {
      var handler_scope = scope;
      if(!$.isFunction(handler)) {
        if(handler.scope)
          handler_scope = handler.scope;
        handler = handler.handler;
      }
      _this.addListener(name, handler, handler_scope);
    });
  },
  
  /**
   * Add listener
   * 
   * @param {String} name
   * @param {Function} handler
   * @param {Object} scope
   */
  addListener: function(name, handler, scope) {
    if(!(this.events[name] instanceof Array))
      this.events[name] = [];
      
    this.events[name].push([handler, scope]);  },
  
  /**
   * Alias of addListeners
   * 
   * @param {String} name
   * @param {Function} handler
   * @param {Object} scope
   */
  on: function(name, handler, scope) {
    this.addListener(name, handler, scope);
  },
  
  /**
   * Fire event
   * 
   * @param {Object} name
   * @param {Array} args
   */
  fireEvent: function(name, args) {
    if(!(this.events[name] instanceof Array))
      return true;
      
    var result = true;
    if(!args)
      args = [];
      
    for(var i = 0, l = this.events[name].length; i < l; i ++) {
      var handler = this.events[name][i][0];
      var scope = this.events[name][i][1];
      if(!scope)
        scope = window;
    
      if(handler.apply(scope, args) === false)
        result = false;
    }    
    return result;
  }
}
