/**
 * Offset
 * 
 * @param {Object} left
 * @param {Object} top
 */
Offset = function(left, top) {
  this.left = 0;
  this.top = 0;
  
  this.setup(left, top);
}

Offset.prototype = {
  setup: function(left, top) {
    if(left instanceof Object) {
      this.left = left.left;
      this.top = left.top;
    } else {
      if(left !== undefined) this.left = left;
      if(top !== undefined) this.top = top;
    }
    return this;
  },
  
  add: function(left, top) {
    var offset = new Offset(left, top);
    this.left += offset.left;
    this.top += offset.top;
    return this;
  },
  
  sub: function(left, top) {
    var offset = new Offset(left, top);
    this.left -= offset.left;
    this.top -= offset.top;
    return this;
  },
  
  floor: function() {
    return new Offset(Math.floor(this.left), Math.floor(this.top));
  },
  
  isEqual: function(left, top) {
    var offset = new Offset(left, top);
    return offset.left == this.left && offset.top == this.top;
  },
  
  getCopy: function() {
    return new Offset(this);
  }
}

Size = function(width, height) {
  this.width = 0;
  this.height = 0;
  
  this.setup(width, height);
}

Size.prototype = {
  setup: function(width, height) {
    if(width instanceof Object) {
      this.width = width.width;
      this.height = width.height;
    } else {
      if(width !== undefined) this.width = width;
      if(height !== undefined) this.height = height;      
    }
    return this;
  },
  
  add: function(width, height) {
    var size = new Size(width, height);
    this.width += size.width;
    this.height += size.height;
    return this;
  },
  
  sub: function(width, height) {
    var size = new Size(width, height);
    this.width -= size.width;
    this.height -= size.height;
    return this;
  },
  
  floor: function() {
    return new Size(Math.floor(this.width), Math.floor(this.height));
  },
  
  isEqual: function(width, height) {
    var size = new Size(width, height);
    return size.width == this.width && size.height == this.height;
  },
  
  getCopy: function() {
    return new Size(this);
  }
}

Frame = function(left, top, width, height) {
  this.offset = new Offset();
  this.size = new Size();
  
  this.setup(left, top, width, height);
}

Frame.prototype = {
  setup: function(left, top, width, height) {
    if(left instanceof Object) {
      this.setOffset(left);
      if(top) this.setSize(top);
    } else {
      this.offset.setup(left, top);
      this.size.setup(width, height);
    }
    return this;
  },
  
  setSize: function(width, height) {
    this.size.setup(width, height);
    return this;
  },
  
  setOffset: function(left, top) {
    this.offset.setup(left, top);
    return this;
  },
  
  getRight: function() {
    return this.offset.left + this.size.width;
  },
  
  getBottom: function() {
    return this.offset.top + this.size.height;
  },
  
  getXC: function() {
    return this.offset.left + this.size.width / 2;
  },
  
  getYC: function() {
    return this.offset.top + this.size.height / 2;
  },
  
  getCenter: function() {
    return new Offset(this.getXC(), this.getYC());
  },
  
  getCopy: function() {
    return new Frame(this.offset, this.size);
  },
  
  setRight: function(right) {
    this.offset.left = right - this.size.width;
  },
  
  setBottom: function(bottom) {
    this.offset.top = bottom - this.size.height;
  },
  
  floor: function() {
    return new Frame(this.offset.floor(), this.size.floor());
  },
  
  isEqual: function(frame) {
    return this.offset.isEqual(frame.offset) && this.size.isEqual(frame.size);
  },
  
  testX: function(x) {
    return x >= this.offset.left
           && x <= this.getRight();
  },
  
  testY: function(y) {
    return y >= this.offset.top
           && y <= this.getBottom();
  },
  
  testPoint: function(x, y) {
    return this.testX(x) && this.testY(y);
  }
  
}
