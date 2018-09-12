TableSorter = function(config) {
  // configurable {
  this.selector = ''; // required table selector
  this.rows_selector = 'tr.sorted-row';
  // }
  $.extend(this, config);
  
  this.sort_directions = {}
  this.last_sort = '';
}

TableSorter.prototype = {
  start: function() {
    this.initEvents();
    
    return this;
  },
  
  initEvents: function() {
    $('.has-sort', this.getEl()).click($.proxy(this.onClickSortingHeader, this));
  },
  
  sort: function(column) {
    var direction = this.sort_directions[column] || -1;
    if(column == this.last_sort)
      direction = -direction;
    else
      direction = 1;
    
    this.sort_directions[column] = direction;
    this.last_sort = column;
    
    var rows = [];
    $(this.rows_selector, this.getEl()).each(function() {
      var $row = $(this);
      var sort_data = $('td:eq(' + column + ')', $row).data('sort-value');
      rows.push([$row.generateId().attr('id'), sort_data]);
    });
    
    if(!rows)
      return;
    
    var $first = $(document.getElementById(rows[0][0]))
    
    rows.sort(function(a, b) {
      var float_a = parseFloat(a[1]), float_b = parseFloat(b[1]);
      var result = 0;
      if(isNaN(float_a) || isNaN(float_b)) {
        if(a[1] < b[1])
          result = -1;
        else if(a[1] > b[1])
          result = 1;
      } else {
        result = float_a - float_b;
      }
      result *= direction
      
      return result;
    });
    
    var $row = $(document.getElementById(rows.shift()[0]));
    if($row.attr('id') != $first.attr('id')) 
      $first.before($row);
    
    for(var i = 0, l = rows.length; i < l; i ++) {
      var $next_row = $(document.getElementById(rows[i][0]));
      $row.after($next_row);
      $row = $next_row;
    }
    
    this.restrip();
  },
  
  restrip: function() {
    $(this.rows_selector, this.getEl()).removeClass('even').filter(function(i) {
      return i % 2 != 0;
    }).addClass('even');
  },
  
  onClickSortingHeader: function(e) {
    var column = $(e.target).closest('td').prevAll().length;
    this.sort(column);
    
    return false;
  },
  
  getEl: function() {
    return $(this.selector);
  }
}