  /**
 * Auto pager
 */
AutoPager = function(config) {
  config = $.extend({
    markerSelector: '', // required
    /**
     * Selector of a place to load content.
     * Equals markerSelector by default.
     */
    placeHolder: false,
    listUrl: '', // required
    loaderImg: '/images/page-loader.gif',
    offsetParam: 'offset',
    pageLen: 0 // required
  }, config);
  
  this.addEvents({
    load: true
  });
  
  AutoPager.superclass.constructor.call(this, config);

  this.isLoading = false;
  this.curOffset = 0;
  this.finished = false;
  this.params = {}

  if(!this.placeHolder)
    this.placeHolder = this.markerSelector;
}

utils.extend(AutoPager, utils.Observable, {
  start: function() {
    this.initEvents();
    
    this.loadIf();
    
    return this;
  },
  
  initEvents: function() {
    $(window).scroll($.proxy(this.onPageScroll, this));
  },
  
  loadIf: function() {
    if(this.checkForLoad())
      this.load();
  },
  
  load: function() {
    this.isLoading = true;
    
    this.params[this.offsetParam] = this.curOffset + this.pageLen;
    $.get(this.listUrl, this.params, $.proxy(this.onLoad, this));
    
    this.showLoader();
  },
  
  reload: function() {
    this.getPlaceHolder().prevAll().remove();
    this.curOffset = 0;
    this.finished = false;
    this.load();
  },
  
  setParam: function(name, value) {
    this.params[name] = value;
  },
  
  showLoader: function() {
    this.getMarker().html('<img src="' + this.loaderImg + '" alt="загрузка..."/>');
  },
  
  hideLoader: function() {
    this.getMarker().empty();
  },
  
  checkForLoad: function() {
    if(this.isLoading || this.finished)
      return false;
    
    var frame = utils.Effects.Frame.getViewFrame();
    return frame.testY(this.getMarker().offset().top);
  },
  
  getMarker: function() {
    return $(this.markerSelector);
  },

  getPlaceHolder: function() {
    return $(this.placeHolder);
  },
  
  onPageScroll: function() {
    this.loadIf();
  },
  
  onLoad: function(data) {
    data = $.trim(data);
    if(data == '') {
      this.finished = true;
    } else {
      this.curOffset += this.pageLen;
      this.getPlaceHolder().before(data);
    }
    
    this.isLoading = false;    
    this.hideLoader();
    
    this.fireEvent('load', [ this ]);
  }
});