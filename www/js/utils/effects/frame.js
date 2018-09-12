ns('utils.Effects.Frame');

utils.Effects.Frame = {
  getViewCenter: function() {
    var frame = utils.Effects.Frame.getViewFrame();
    return new Offset(frame.offset.left + frame.size.width / 2, frame.offset.top + frame.size.height / 2);
  },
  
  getViewFrame: function() {
    return new Frame(utils.Effects.Frame.getViewOffset(), utils.Effects.Frame.getViewSize());
  },
  
  getViewSize: function() {
    var height = document.compatMode=='CSS1Compat' ?document.documentElement.clientHeight : document.body.clientHeight;
    var width = document.compatMode=='CSS1Compat' ?document.documentElement.clientWidth : document.body.clientWidth;
    return new Size(width, height);
  },
  
  getViewOffset: function() {
    var top = self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
    var left = self.pageXOffset || (document.documentElement && document.documentElement.scrollLeft) || (document.body && document.body.scrollLeft);
    return new Offset(left, top);
  },
  
  getBodyOffset: function() {
    return $('body').offset();
  },
  
  scaleRectangleIntoRectangle: function(src, dst) {
    var scale = 1;
    var scale_w = dst.width / src.width;
    var scale_h = dst.height / src.height;
    if(scale_w > 1 && scale_h > 1)
      scale = 1;
    else if(scale_w < 1 && scale_h < 1)
    {
      if(scale_w > scale_h)
        scale = scale_h;
      else
        scale = scale_w;
    }
    else if(scale_w < 1)
      scale = scale_w;
    else
      scale = scale_h;
      
    return new Size(src.width * scale, src.height * scale);
  }
};