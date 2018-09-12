ns('utils.Effects');

/**
 * Simple image preloader
 */
utils.Effects.SimpleImagePreloader = function() {
  this.images = {};
}

utils.Effects.SimpleImagePreloader._instance = null;
utils.Effects.SimpleImagePreloader.instance = function() {
  if(!utils.Effects.SimpleImagePreloader._instance)
    utils.Effects.SimpleImagePreloader._instance = new utils.Effects.SimpleImagePreloader();
    
  return utils.Effects.SimpleImagePreloader._instance;
}

utils.Effects.SimpleImagePreloader.prototype = {
  add: function(src) {
    if(src instanceof Array) {
      for(var i = 0, l = src.length; i < l; i++) 
        this.add(src[i]);
    } else {
      if(!this.images[src]) {
        this.images[src] = new Image();
        this.images[src].src = src; 
      }
    }    
  }
}

function preload_image(src) {
  utils.Effects.SimpleImagePreloader.instance().add(src);
}