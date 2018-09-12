ns('utils.Effects');

/**
 * Magnifier mask
 * 
 * @param {Object} config
 */
utils.Effects.Mask = function(config) {
  // configurable {
  this.opacity = utils.Effects.Mask.MASK_OPACITY;
  this.mask_speed = utils.Effects.Mask.SPEED;
  // }
  
  this.shown = false;
  this.el = null;
  
  this.addEvents({
    click: true
  });
  
  utils.Effects.Mask.superclass.constructor.call(this, config);
}

utils.Effects.Mask.MASK_OPACITY = 0.85;
utils.Effects.Mask.SPEED = 'fast';

utils.extend(utils.Effects.Mask, utils.Observable, {
  initEvents: function() {
    $(window).resize(this.onUpdateFrame.createDelegate(this))
             .scroll(this.onUpdateFrame.createDelegate(this));
             
    this.getEl().click(this.onClick.createDelegate(this));
  },
  
  show: function(callback) {
    this.shown = true;
    this._updatePosition();
    this.getEl().fadeTo(this.mask_speed, this.opacity, callback);
  },
  
  hide: function() {
    var _this = this;
    this.getEl().fadeTo(this.mask_speed, 0, function() {
      _this.shown = false;
      $(this).css({
        top: -1000,
        left: -1000,
        width: 0,
        height: 0
      });
    });
  },
  
  _updatePosition: function() {
    this.getEl().css({
      top: 0,
      left: 0
    })
    .width('100%').height($(document).height());

  },
  
  getEl: function() {
    if(!this.el) {
      this.el = this._createEl().getIdSelector();  
      this.initEvents();
    }
    return $(this.el);
  },
  
  onUpdateFrame: function() {
    if(this.shown)
      this._updatePosition();
  },
  
  onClick: function() {
    this.fireEvent('click', [this]);
  },
  
  _createEl: function() {
    return $('<div class="background_mask">&nbsp;</div>').css({
      position: 'absolute',
      top: -1000,
      left: -1000,
      width: 0,
      height: 0
    }).appendTo('body').fadeTo(0, 0);
  }
});


utils.Effects.Mask.Singletone = {
  _instance: null,
  
  instance: function() {
    if(!utils.Effects.Mask.Singletone._instance) {
      utils.Effects.Mask.Singletone._instance = new utils.Effects.Mask();
    }
    return utils.Effects.Mask.Singletone._instance;
  },
  
  show: function() {
    utils.Effects.Mask.Singletone.instance().show();
  },
  
  hide: function() {
    utils.Effects.Mask.Singletone.instance().hide();
  }
}
