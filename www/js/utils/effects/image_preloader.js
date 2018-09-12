ns('utils.Effects');

/**
 * Image preloader
 */
utils.Effects.ImagePreloader = function(config) {
  this.loaded = false;
  this.size = new Size();
  this.selector = null;
  this.src = null;
  this.loading = false;
  
  this.addEvents({
    load: true
  });
  
  utils.Effects.ImagePreloader.superclass.constructor.call(this, config);
}

utils.extend(utils.Effects.ImagePreloader, utils.Observable, {
  load: function(src) {
    this.loaded = false;
    this.loading = true;
    this.src = src;
    this.getImgEl().attr('src', src);
  },
  
  getSrc: function() {
    return this.src;
  },
  
  isLoaded: function() {
    return this.loaded;
  },
  
  isLoading: function() {
    return this.loading;
  },
  
  getSize: function() {
    return this.size.getCopy();
  },
  
  onLoad: function(e) {
    this.size.setup(e.target.width, e.target.height);
    this.loaded = true;
    this.loading = false;
    this.removeEl();
    this.fireEvent('load', [this]);
  },
  
  cancel: function() {
    this.loading = false;
    this.removeEl();
  },
  
  removeEl: function() {
    if(!this.selector)
      return;
      
    this.getImgEl().unbind('load', this.onLoad);
    this.getEl().remove();
    this.selector = null;
  },
  
  getEl: function() {
    if(!this.selector)
      this.selector = this._createPreloadImg();
      
    return $(this.selector);
  },
  
  getImgEl: function() {
    return $('img', this.getEl());
  },
  
  _createPreloadImg: function() {
    var $el = $('<div style="position: absolute; top: -10px; left: -10px; width: 1px; height: 1px; overflow: hidden;"></div>');
    var $img = $('<img>').load(this.onLoad.createDelegate(this));
    
    return $el.append($img).appendTo('body').getIdSelector();
  }
});
