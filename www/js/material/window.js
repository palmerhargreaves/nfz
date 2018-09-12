MaterialWindow = function(config) {
  // configurable {
  this.selector = ''; // required a window selector
  this.url = ''; // required url to load material data
  this.hide_page_indicator_delay = 2000;
  // }
  $.extend(this, config);
  
  this.web_preview_count = 0;
  this.cur_web_preview = 0;
  this.web_previews = [];
  this.hide_page_indicator_timer = null;
}

MaterialWindow.prototype = {
  start: function() {
    this.initEvents();
    
    return this;
  },
  
  initEvents: function() {
    this.getCloseButton().click($.proxy(this.onClickCloseButton, this));
    this.getWebPreview().load($.proxy(this.onLoadPreview, this));
    this.getPrevButton().click($.proxy(this.onClickPrev, this));
    this.getNextButton().click($.proxy(this.onClickNext, this));
    this.getFilePreview().click($.proxy(this.onClickButton, this));
  },
  
  load: function(id) {
    $.get(this.url, { id: id }, $.proxy(this.onLoad, this));
  },
  
  next: function() {
    if(this.cur_web_preview ++ >= this.web_previews.length - 1)
      this.cur_web_preview = 0;
    
    this.setWebPreviewByPos(this.cur_web_preview);
  },
  
  prev: function() {
    if(this.cur_web_preview -- <= 0)
      this.cur_web_preview = this.web_previews.length - 1;
    
    this.setWebPreviewByPos(this.cur_web_preview);
  },
  
  setWebPreviewByPos: function(pos) {
    this.cur_web_preview = pos;
    var src = '/uploads/materials/web_preview/' + this.web_previews[pos];
    if(src != this.getWebPreview().attr('src')) {
      this.showLoader();
      this.getWebPreview().attr('src', '/uploads/materials/web_preview/' + this.web_previews[pos]);
    } else {
      this.showWebPreview();
    }
    this.updatePageIndicator();
  },
  
  showWebPreview: function() {
    this.getLoader().hide();
    this.getWebPreview().css('visibility', 'visible');
  },
  
  showLoader: function() {
    this.getWebPreview().css('visibility', 'hidden');
    this.getLoader().show();
  },
  
  applyData: function(data) {
    this._setupHeader(data);
    this._setupWebPreviews(data);
    this._setupFilePreview(data);
    this._setupEditorLink(data);
    this._setupSources(data);
    
    this.show();
  },
  
  _setupWebPreviews: function(data) {
    this.web_preview_count = data.web_previews.length;
    this.web_previews = data.web_previews;
    
    if(data.web_previews.length == 0) {
      this.getWebPreview().hide();
    } else {
      this.setWebPreviewByPos(0);
    }
    
    if(data.web_previews.length > 1) {
      this.getNextButton().show();
      this.getPrevButton().show();
    } else {
      this.getNextButton().hide();
      this.getPrevButton().hide();
    }
  },

  _setupHeader: function(data) {
    this.getHeader().html(data.name);
  },
  
  _setupFilePreview: function(data) {
    if(data.file_preview) {
      this.getFilePreview().show();
      this.getFilePreviewName().html('Превью ' + data.file_preview.ext.toUpperCase() + ' (' + data.file_preview.smart_size + ')');
      this.getFilePreviewLink().attr('href', '/uploads/materials/preview/' + data.file_preview.file);
    } else {
      this.getFilePreview().hide();
    }
  },
  
  _setupSources: function(data) {
    
    for(var i = 0, l = data.sources.length; i < l; i ++) {
      var source = data.sources[i];
      var $markup = $('<a target="_blank" href="/activity/material/download/' + source.id + '"><i><b>' + source.ext.toUpperCase() + '</b></i><span>' + source.name + ' ' + source.ext.toUpperCase() + ' (' + source.smart_size + ')</span></a>');

      $markup.appendTo(this.getSourcesBlock()).click($.proxy(this.onClickButton, this));
    }
  },

  _setupEditorLink: function(data) {
    this.getSourcesBlock().empty();
    if(data.editor_link != undefined) {
      var $markup = $('<div class="button"><img src="/images/red_ico.jpg" alt="">'   
                    + '<a class="name" target="_blank" href="' + data.editor_link + '">Редактировать</a>'
                    + '</div>');

      $markup.appendTo(this.getSourcesBlock()).click($.proxy(this.onClickButton, this));
    }
  },
  
  updatePageIndicator: function() {
    if(this.web_preview_count > 1) {
      this.getPageIndicatorNumbers().html((this.cur_web_preview + 1) + ' из ' + this.web_preview_count);
      this.startHidePageIndicatorTimer();
      this.getPageIndicator().stop().css('opacity', 1).show();
    } else {
      this.getPageIndicator().hide();
    }
  },
  
  show: function() {
    $('body').append(this.getWindow());
    this.getWindow().show();
    var top = $(document).scrollTop() + 15;
    $(this.getWindow()).offset({top: top});
  },
  
  hide: function() {
    this.getWindow().hide();
  },
  
  updatePosition: function() {
    var width = this.getWindow().width();
    this.getWindow().css('margin-left', - width / 2);
  },
  
  startHidePageIndicatorTimer: function() {
    this.stopHidePageIndicatorTimer();
    
    this.hide_page_indicator_timer = setTimeout($.proxy(this.onAutoHidePageIndicator, this), this.hide_page_indicator_delay);
  },
  
  stopHidePageIndicatorTimer: function() {
    if(this.hide_page_indicator_timer) {
      clearTimeout(this.hide_page_indicator_timer);
      this.hide_page_indicator_timer = null;
    }
  },
  
  hasServeralWebPreviews: function() {
    return this.web_preview_count > 0;
  },
  
  getWebPreview: function() {
    return $('.web-preview', this.getWindow());
  },
  
  getNextButton: function() {
    return $('.next', this.getWindow());
  },
  
  getPrevButton: function() {
    return $('.prev', this.getWindow());
  },
  
  getPageIndicatorNumbers: function() {
    return $('.numbers', this.getPageIndicator());
  },
  
  getPageIndicator: function() {
    return $('.page-indicator', this.getWindow());
  },

  getFilePreviewLink: function() {
    return $('a', this.getFilePreview());
  },
  
  getFilePreviewIcon: function() {
    return $('.ext-icon', this.getFilePreview());
  },
  
  getFilePreviewName: function() {
    return $('.name', this.getFilePreview());
  },
  
  getFilePreview: function() {
    return $('.preview', this.getWindow());
  },
  
  getSourcesBlock: function() {
    return $('.mat-popup-buttons', this.getWindow());
  },
  
  getCloseButton: function() {
    return $('.close', this.getWindow());
  },
  
  getHeader: function() {
    return $('.mat-popup-title', this.getWindow());
  },
  
  getLoader: function() {
    return $('.loader', this.getWindow());
  },
  
  getWindow: function() {
    return $(this.selector);
  },
  
  onLoad: function(data) {
    this.applyData(data);
  },
  
  onClickCloseButton: function() {
    this.hide();
  },
  
  onLoadPreview: function() {
    this.getWindow().width('auto');
    setTimeout($.proxy(function() {
      this.updatePosition();
      this.showWebPreview();
      this.getWindow().width(this.getWindow().width());
    }, this), 50);
  },
  
  onClickNext: function() {
    this.next();
  },
  
  onClickPrev: function() {
    this.prev();
  },
  
  onClickButton: function(e) {
    if($(e.target).closest('a').length == 0) {
      $('a', e.target).clickAnchor();
    }
  },
  
  onAutoHidePageIndicator: function() {
    if(this.hasServeralWebPreviews())
      this.getPageIndicator().fadeOut('slow');
  }
}